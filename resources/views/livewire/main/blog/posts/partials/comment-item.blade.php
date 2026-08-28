@php
    $user = $comment->user;
    $isGuest = !$user;
    $avatarUrl = $user?->avatar_url;
    $initials = $user?->initials ?? '?';
    $isVerified = $comment->is_verified;
    $isReply = !is_null($comment->parent_id);
@endphp

<div class="comment-item d-flex align-items-start gap-3 {{ $isReply ? 'is-reply' : '' }}"
    id="comment-{{ $comment->id }}">
    <!-- Avatar -->
    @if($isGuest)
        <img src="{{ asset('storage/profiles/default-profile.jpg') }}" alt="Guest" class="comment-avatar">
    @elseif($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="comment-avatar">
    @else
        <div class="comment-avatar-initials">{{ $initials }}</div>
    @endif

    <!-- Comment body -->
    <div class="comment-body">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="comment-author">{{ $isGuest ? $comment->guest_name : $user->name }}</span>
            @if(!$isVerified)
                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Pending</span>
            @endif
            <span class="comment-date"><i
                    class="fal fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}</span>
        </div>

        @if($editingCommentId === $comment->id)
            <div class="mt-2">
                <form wire:submit.prevent="updateComment">
                    <textarea class="form-control" wire:model.defer="body" rows="2" style="font-size: 16px;"></textarea>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <button type="button" class="btn btn-sm btn-secondary" wire:click="cancelEdit">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <div class="comment-text">{{ $comment->body }}</div>
        @endif

        {{-- ACTIONS – only shown if comments are enabled --}}
        @if($allowComments)
            <div class="comment-actions">
                @if(!$editingCommentId && !$editingReplyId)
                    <a href="#" wire:click.prevent="toggleReplyForm({{ $comment->id }})" class="reply-toggle">
                        <i class="fal fa-reply me-1"></i>
                        {{ $replyingTo === $comment->id ? 'Cancel' : 'Reply' }}
                    </a>
                @endif

                @auth
                    @if(Auth::id() === $comment->user_id)
                        @if($isReply)
                            <a href="#" wire:click.prevent="editReply({{ $comment->id }})">
                                <i class="fal fa-pencil me-1"></i> Edit
                            </a>
                            <a href="#" wire:click.prevent="deleteReply({{ $comment->id }})"
                                wire:confirm="Delete this reply? This cannot be undone.">
                                <i class="fal fa-trash-alt me-1"></i> Delete
                            </a>
                        @else
                            <a href="#" wire:click.prevent="editComment({{ $comment->id }})">
                                <i class="fal fa-pencil me-1"></i> Edit
                            </a>
                            <a href="#" wire:click.prevent="deleteComment({{ $comment->id }})"
                                wire:confirm="Delete this comment and all its replies? This cannot be undone.">
                                <i class="fal fa-trash-alt me-1"></i> Delete
                            </a>
                        @endif
                    @endif
                @endauth
            </div>
        @endif

        {{-- Inline Reply Form – only if comments enabled --}}
        @if($allowComments && $replyingTo === $comment->id)
            <div class="reply-form-inline">
                <form wire:submit.prevent="saveReply({{ $comment->id }})">
                    @guest
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="text" class="form-control form-control-sm" placeholder="Your Name *"
                                    wire:model.defer="replyGuestName" required style="font-size: 16px;">
                                @error('replyGuestName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <input type="email" class="form-control form-control-sm" placeholder="Your Email *"
                                    wire:model.defer="replyGuestEmail" required style="font-size: 16px;">
                                @error('replyGuestEmail') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endguest

                    <div class="mb-2">
                        <textarea class="form-control @error('replyBody') is-invalid @enderror" wire:model.defer="replyBody"
                            rows="2" placeholder="Write your reply..." style="font-size: 16px;"></textarea>
                        @error('replyBody') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="font-size: 1rem;">
                        {{ $editingReplyId ? 'Update Reply' : 'Post Reply' }}
                    </button>
                    @if($editingReplyId)
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelReply"
                            style="font-size: 1rem;">Cancel</button>
                    @endif

                    @guest
                        <p class="text-muted small mt-1 mb-0"><i class="fal fa-info-circle"></i> You'll receive a verification
                            email if this is your first reply.</p>
                    @endguest
                </form>
            </div>
        @endif
    </div> <!-- /comment-body -->
</div> <!-- /comment-item -->

{{-- Recursive replies – they also need the $allowComments flag passed down --}}
@if($comment->repliesRecursive->isNotEmpty())
    @foreach($comment->repliesRecursive as $reply)
        @include('livewire.main.blog.posts.partials.comment-item', [
            'comment' => $reply,
            'allowComments' => $allowComments
        ])
    @endforeach
@endif