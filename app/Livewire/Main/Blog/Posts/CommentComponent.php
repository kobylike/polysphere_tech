<?php

namespace App\Livewire\Main\Blog\Posts;

use App\Mail\CommentVerificationMail;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CommentComponent extends Component
{
    use WithPagination;

    public Post $post;

    // ─── Comment form ──────────────────────────────────────────────
    public $body = '';
    public $guestName = '';
    public $guestEmail = '';

    // ─── Reply form ────────────────────────────────────────────────
    public $replyBody = '';
    public $replyGuestName = '';
    public $replyGuestEmail = '';
    public $replyingTo = null;
    public $editingReplyId = null;

    // ─── Edit top-level comment ────────────────────────────────────
    public $editingCommentId = null;

    // ─── Honeypot & timing ─────────────────────────────────────────
    public $honeypot = '';
    public $renderedAt = '';

    // ─── Load more ──────────────────────────────────────────────────
    public $perPage = 5;

    // ─── State ──────────────────────────────────────────────────────
    public $submitted = false;

    protected int $guestCommentLimit = 3;
    protected int $guestCommentWindowSeconds = 3600;

    protected $rules = [
        'body' => 'required|string|min:2|max:5000',
        'guestName' => 'required_if:user_id,null|string|max:100',
        'guestEmail' => 'required_if:user_id,null|email|max:255',
        'replyBody' => 'required|string|min:2|max:5000',
        'replyGuestName' => 'required_if:auth,null|string|max:100',
        'replyGuestEmail' => 'required_if:auth,null|email|max:255',
    ];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->renderedAt = now()->valueOf();

        if (session()->has('verified_guest')) {
            $guest = session('verified_guest');
            $this->guestName = $guest['name'] ?? '';
            $this->guestEmail = $guest['email'] ?? '';
            $this->replyGuestName = $guest['name'] ?? '';
            $this->replyGuestEmail = $guest['email'] ?? '';
        }
    }

    public function getComments()
    {
        return $this->post->comments()
            ->whereNull('parent_id')
            ->visible()
            ->with(['user', 'repliesRecursive.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }

    // ────────────────────────────────────────────────────────────
    //  Live refresh (called by wire:poll)
    // ────────────────────────────────────────────────────────────

    /**
     * Called every 30s by wire:poll.visible.30s on the root <div>.
     *
     * We skip the refresh if the visitor is actively composing something,
     * so their draft never gets wiped mid-typing and the edit-form
     * doesn't flicker while it's open.
     */
    public function refreshComments()
    {
        // Skip while user is typing a new comment or reply
        if (trim((string) $this->body) !== '' || trim((string) $this->replyBody) !== '') {
            return;
        }

        // Skip while user is editing an existing comment / reply
        if ($this->editingCommentId !== null || $this->editingReplyId !== null) {
            return;
        }

        // Skip while a reply form is open
        if ($this->replyingTo !== null) {
            return;
        }

        // Nothing to do explicitly — the mere fact that Livewire re-renders
        // causes getComments() to re-run and pick up any new rows.
    }

    // ────────────────────────────────────────────────────────────
    //  Submit top-level comment
    // ────────────────────────────────────────────────────────────

    public function submit()
    {
        $this->validateOnly('body');
        if (!Auth::check()) {
            $this->validateOnly('guestName');
            $this->validateOnly('guestEmail');
        }

        // Honeypot — silently fake success for bots
        if (!empty($this->honeypot)) {
            $this->fakeSuccess();
            return;
        }

        // Timing trap — bots submit too fast
        $elapsed = now()->valueOf() - (float) $this->renderedAt;
        if ($elapsed < 2000) {
            $this->fakeSuccess();
            return;
        }

        if ($this->containsProfanity($this->body)) {
            $this->addError('body', 'Please avoid using inappropriate language.');
            return;
        }

        if (!$this->enforceGuestRateLimit('body')) {
            return;
        }

        $data = [
            'post_id'    => $this->post->id,
            'body'       => $this->body,
            'parent_id'  => null,
            'ip_address' => request()->ip(),
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['guest_name']         = $this->guestName;
            $data['guest_email']        = $this->guestEmail;
            $data['verification_token'] = Str::random(64);
        }

        $comment = Comment::create($data);

        if (!Auth::check()) {
            $trusted = Comment::where('guest_email', $this->guestEmail)
                ->whereNotNull('verified_at')
                ->exists();

            if ($trusted) {
                $comment->update([
                    'verified_at'        => now(),
                    'verification_token' => null,
                ]);
                $this->submitted = false;
                session()->put('verified_guest', [
                    'name'  => $this->guestName,
                    'email' => $this->guestEmail,
                ]);
            } else {
                $url = route('comment.verify', ['token' => $comment->verification_token]);
                Mail::to($comment->guest_email)->send(new CommentVerificationMail($comment, $url));
                $this->submitted = true;
                $this->reset(['body', 'guestName', 'guestEmail']);
                $this->dispatch(
                    'notify',
                    type: 'info',
                    message: 'Please check your email to verify your comment.'
                );
            }
        } else {
            $this->reset(['body', 'editingCommentId']);
        }

        $this->renderedAt = now()->valueOf();
        $this->dispatch('commentSubmitted');
    }

    // ────────────────────────────────────────────────────────────
    //  Submit reply
    // ────────────────────────────────────────────────────────────

    public function saveReply($parentId)
    {
        $this->validateOnly('replyBody');
        if (!Auth::check()) {
            $this->validateOnly('replyGuestName');
            $this->validateOnly('replyGuestEmail');
        }

        if ($this->containsProfanity($this->replyBody)) {
            $this->addError('replyBody', 'Please avoid using inappropriate language.');
            return;
        }

        if (!$this->enforceGuestRateLimit('replyBody')) {
            return;
        }

        $data = [
            'post_id'    => $this->post->id,
            'body'       => $this->replyBody,
            'parent_id'  => $parentId,
            'ip_address' => request()->ip(),
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['guest_name']         = $this->replyGuestName;
            $data['guest_email']        = $this->replyGuestEmail;
            $data['verification_token'] = Str::random(64);
        }

        $reply = Comment::create($data);

        if (!Auth::check()) {
            $trusted = Comment::where('guest_email', $this->replyGuestEmail)
                ->whereNotNull('verified_at')
                ->exists();

            if ($trusted) {
                $reply->update([
                    'verified_at'        => now(),
                    'verification_token' => null,
                ]);
                session()->put('verified_guest', [
                    'name'  => $this->replyGuestName,
                    'email' => $this->replyGuestEmail,
                ]);
            } else {
                $url = route('comment.verify', ['token' => $reply->verification_token]);
                Mail::to($reply->guest_email)->send(new CommentVerificationMail($reply, $url));
                $this->dispatch(
                    'notify',
                    type: 'info',
                    message: 'Please check your email to verify your reply.'
                );
            }
        }

        $this->reset([
            'replyBody',
            'replyGuestName',
            'replyGuestEmail',
            'replyingTo',
            'editingReplyId',
        ]);
        $this->renderedAt = now()->valueOf();
        $this->dispatch('commentSubmitted');
    }

    // ────────────────────────────────────────────────────────────
    //  Edit / delete top-level comment
    // ────────────────────────────────────────────────────────────

    public function editComment($id)
    {
        $comment = Comment::findOrFail($id);

        if (!$this->canModifyComment($comment)) {
            $this->dispatch('notify', type: 'error', message: 'You cannot edit this comment.');
            return;
        }

        $this->editingCommentId = $id;
        $this->body = $comment->body;
    }

    public function cancelEdit()
    {
        $this->editingCommentId = null;
        $this->body = '';
    }

    public function updateComment()
    {
        $this->validate(['body' => 'required|string|min:2|max:5000']);

        $comment = Comment::findOrFail($this->editingCommentId);

        if (!$this->canModifyComment($comment)) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }

        if ($this->containsProfanity($this->body)) {
            $this->addError('body', 'Please avoid using inappropriate language.');
            return;
        }

        $comment->update(['body' => $this->body]);
        $this->editingCommentId = null;
        $this->body = '';
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);

        if (!$this->canModifyComment($comment)) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }

        $comment->delete();
        $this->dispatch('commentSubmitted');
    }

    // ────────────────────────────────────────────────────────────
    //  Edit / delete reply
    // ────────────────────────────────────────────────────────────

    public function editReply($id)
    {
        $reply = Comment::findOrFail($id);

        if (!$this->canModifyComment($reply)) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }

        $this->editingReplyId = $id;
        $this->replyBody = $reply->body;
        $this->replyingTo = $reply->parent_id;
    }

    public function cancelReply()
    {
        $this->editingReplyId = null;
        $this->replyBody = '';
        $this->replyGuestName = '';
        $this->replyGuestEmail = '';
        $this->replyingTo = null;
    }

    public function deleteReply($id)
    {
        $reply = Comment::findOrFail($id);

        if (!$this->canModifyComment($reply)) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }

        $reply->delete();
        $this->dispatch('commentSubmitted');
    }

    public function toggleReplyForm($id)
    {
        if ($this->replyingTo === $id) {
            $this->replyingTo = null;
            $this->replyBody = '';
            $this->replyGuestName = '';
            $this->replyGuestEmail = '';
            $this->editingReplyId = null;
        } else {
            $this->replyingTo = $id;
            $this->replyBody = '';
            $this->replyGuestName = '';
            $this->replyGuestEmail = '';
            $this->editingReplyId = null;

            $parent = Comment::find($id);
            if ($parent) {
                $this->replyBody = '@' . $parent->author_name . ' ';
                if (session()->has('verified_guest')) {
                    $guest = session('verified_guest');
                    $this->replyGuestName = $guest['name'] ?? '';
                    $this->replyGuestEmail = $guest['email'] ?? '';
                }
            }
        }
    }

    // ────────────────────────────────────────────────────────────
    //  Ownership & rate-limiting helpers
    // ────────────────────────────────────────────────────────────

    protected function canModifyComment(Comment $comment): bool
    {
        if (Auth::check()) {
            return (int) Auth::id() === (int) $comment->user_id;
        }

        if ($comment->user_id !== null) {
            return false;
        }

        $verified = session('verified_guest');
        if (!$verified || empty($verified['email']) || empty($comment->guest_email)) {
            return false;
        }

        return strcasecmp($comment->guest_email, $verified['email']) === 0;
    }

    protected function enforceGuestRateLimit(string $errorField): bool
    {
        if (Auth::check()) {
            return true;
        }

        $key = 'guest-comment:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, $this->guestCommentLimit)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError($errorField, "Too many attempts. Please wait {$seconds} seconds.");
            return false;
        }

        RateLimiter::hit($key, $this->guestCommentWindowSeconds);
        return true;
    }

    // ────────────────────────────────────────────────────────────
    //  Profanity filter
    // ────────────────────────────────────────────────────────────

    protected function containsProfanity($text): bool
    {
        $badWords = [
            'fuck',
            'fucking',
            'shit',
            'asshole',
            'bitch',
            'cunt',
            'dick',
            'pussy',
            'motherfucker',
            'bastard',
            'whore',
            'slut',
            'nigger',
            'faggot',
            'retard',
            'cocksucker',
        ];

        $clean = preg_replace('/\s+/', ' ', trim($text));
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';

        return preg_match($pattern, strtolower($clean)) === 1;
    }

    // ────────────────────────────────────────────────────────────
    //  Bot honeypot success
    // ────────────────────────────────────────────────────────────

    protected function fakeSuccess()
    {
        $this->submitted = true;
        $this->reset([
            'body',
            'guestName',
            'guestEmail',
            'replyBody',
            'replyGuestName',
            'replyGuestEmail',
        ]);
        $this->renderedAt = now()->valueOf();
    }

    // ────────────────────────────────────────────────────────────
    //  Render
    // ────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.main.blog.posts.comment-component', [
            'comments' => $this->getComments(),
        ]);
    }
}
