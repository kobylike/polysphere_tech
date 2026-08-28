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

    // ─── Honeypot & timing ────────────────────────────────────────
    public $honeypot = '';
    public $renderedAt = '';

    // ─── Load more ──────────────────────────────────────────────────
    public $perPage = 5;

    // ─── State ──────────────────────────────────────────────────────
    public $submitted = false;

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

    public function submit()
    {
        $this->validateOnly('body');
        if (!Auth::check()) {
            $this->validateOnly('guestName');
            $this->validateOnly('guestEmail');
        }

        if (!empty($this->honeypot)) {
            $this->fakeSuccess();
            return;
        }

        $elapsed = now()->valueOf() - (float) $this->renderedAt;
        if ($elapsed < 2000) {
            $this->fakeSuccess();
            return;
        }

        if ($this->containsProfanity($this->body)) {
            $this->addError('body', 'Please avoid using inappropriate language.');
            return;
        }

        if (!Auth::check()) {
            $key = 'guest-comment:' . request()->ip();
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $this->addError('body', "Too many attempts. Please wait {$seconds} seconds.");
                return;
            }
            RateLimiter::hit($key, 3600);
        }

        $data = [
            'post_id' => $this->post->id,
            'body' => $this->body,
            'parent_id' => null,
            'ip_address' => request()->ip(),
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['guest_name'] = $this->guestName;
            $data['guest_email'] = $this->guestEmail;
            $data['verification_token'] = Str::random(64);
        }

        $comment = Comment::create($data);

        if (!Auth::check()) {
            $trusted = Comment::where('guest_email', $this->guestEmail)
                ->whereNotNull('verified_at')
                ->exists();

            if ($trusted) {
                $comment->update(['verified_at' => now(), 'verification_token' => null]);
                $this->submitted = false;
                session()->put('verified_guest', ['name' => $this->guestName, 'email' => $this->guestEmail]);
            } else {
                $url = route('comment.verify', ['token' => $comment->verification_token]);
                Mail::to($comment->guest_email)->send(new CommentVerificationMail($comment, $url));
                $this->submitted = true;
                $this->reset(['body', 'guestName', 'guestEmail']);
                $this->dispatch('notify', type: 'info', message: 'Please check your email to verify your comment.');
            }
        } else {
            $this->reset(['body', 'editingCommentId']);
        }

        $this->renderedAt = now()->valueOf();
        $this->dispatch('commentSubmitted');
    }

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

        $data = [
            'post_id' => $this->post->id,
            'body' => $this->replyBody,
            'parent_id' => $parentId,
            'ip_address' => request()->ip(),
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['guest_name'] = $this->replyGuestName;
            $data['guest_email'] = $this->replyGuestEmail;
            $data['verification_token'] = Str::random(64);
        }

        $reply = Comment::create($data);

        if (!Auth::check()) {
            $trusted = Comment::where('guest_email', $this->replyGuestEmail)
                ->whereNotNull('verified_at')
                ->exists();

            if ($trusted) {
                $reply->update(['verified_at' => now(), 'verification_token' => null]);
                session()->put('verified_guest', ['name' => $this->replyGuestName, 'email' => $this->replyGuestEmail]);
            } else {
                $url = route('comment.verify', ['token' => $reply->verification_token]);
                Mail::to($reply->guest_email)->send(new CommentVerificationMail($reply, $url));
                $this->dispatch('notify', type: 'info', message: 'Please check your email to verify your reply.');
            }
        }

        $this->reset(['replyBody', 'replyGuestName', 'replyGuestEmail', 'replyingTo', 'editingReplyId']);
        $this->renderedAt = now()->valueOf();
        $this->dispatch('commentSubmitted');
    }

    // ─── Edit Comment ──────────────────────────────────────────────

    public function editComment($id)
    {
        $comment = Comment::findOrFail($id);
        if (Auth::id() !== $comment->user_id) {
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
        if (Auth::id() !== $comment->user_id) {
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

    // ─── Delete (no modal, uses wire:confirm on the frontend) ───────

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        if (Auth::id() !== $comment->user_id) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }
        $comment->delete();
        $this->dispatch('commentSubmitted');
    }

    public function deleteReply($id)
    {
        $reply = Comment::findOrFail($id);
        if (Auth::id() !== $reply->user_id) {
            $this->dispatch('notify', type: 'error', message: 'Permission denied.');
            return;
        }
        $reply->delete();
        $this->dispatch('commentSubmitted');
    }

    public function editReply($id)
    {
        $reply = Comment::findOrFail($id);
        if (Auth::id() !== $reply->user_id) {
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

    protected function containsProfanity($text): bool
    {
        $badWords = [
            'fuck',
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
            'cum',
            'suck',
            'fucking',
            'sucks',
            'piss',
            'pissed',
            'cock',
            'balls',
            'damn',
        ];
        $clean = preg_replace('/\s+/', ' ', trim($text));
        $lower = strtolower($clean);
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';
        return preg_match($pattern, $lower) === 1;
    }

    protected function fakeSuccess()
    {
        $this->submitted = true;
        $this->reset(['body', 'guestName', 'guestEmail', 'replyBody', 'replyGuestName', 'replyGuestEmail']);
    }

    public function render()
    {
        return view('livewire.main.blog.posts.comment-component', [
            'comments' => $this->getComments(),
        ]);
    }
}
