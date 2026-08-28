<div x-data="{
    toasts: []
}"
@notify.window="
    toasts.push({ id: Date.now(), type: $event.detail.type, message: $event.detail.message });
    setTimeout(() => { toasts = toasts.filter(t => t.id !== $event.detail.id); }, 5000);
"
x-cloak
>
    {{-- TOASTS --}}
    <div class="um-toast-container" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div class="um-toast" :class="'um-toast--' + toast.type"
                 x-show="true"
                 x-transition:enter="um-toast-enter"
                 x-transition:enter-start="um-toast-enter-start"
                 x-transition:enter-end="um-toast-enter-end"
                 x-transition:leave="um-toast-leave"
                 x-transition:leave-start="um-toast-leave-start"
                 x-transition:leave-end="um-toast-leave-end">
                <i :class="'fas ' + (toast.type === 'success' ? 'fa-check-circle' : toast.type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle')"></i>
                <span x-text="toast.message"></span>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)"><i class="fas fa-times"></i></button>
            </div>
        </template>
    </div>

    {{-- FALLBACK SESSION FLASH MESSAGES --}}
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- COMMENT COUNT --}}
    <h4 class="postbox__comment-form-title mb-30" style="font-size: 1.4rem;">
        {{ $comments->total() }} Comment{{ $comments->total() > 1 ? 's' : '' }}
    </h4>

    {{-- COMMENT LIST --}}
    @if($comments->count() > 0)
        <div class="comments-list">
            @foreach($comments as $comment)
                @include('livewire.main.blog.posts.partials.comment-item', [
                    'comment' => $comment,
                    'allowComments' => $post->allow_comments
                ])
            @endforeach
        </div>

        @if($comments->hasMorePages())
            <div class="text-center mt-4">
                <button class="btn btn-outline-primary rounded-pill px-4 load-more-btn" wire:click="loadMore" wire:loading.attr="disabled" style="font-size: 1rem;">
                    <span wire:loading.remove>Load More</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Loading…</span>
                </button>
            </div>
        @endif
    @else
        <p class="text-muted" style="font-size: 1rem;">No comments yet.</p>
    @endif

    {{-- COMMENT FORM (only if comments enabled) --}}
    @if($post->allow_comments)
        @auth
            <div class="postbox__comment-form mt-50">
                <h5 class="mb-20" style="font-size: 1.2rem;">{{ $editingCommentId ? 'Edit Comment' : 'Leave a Comment' }}</h5>
                <form wire:submit.prevent="submit">
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="postbox__comment-input">
                                <label style="font-weight: 600; font-size: 1rem;">Your Comment *</label>
                                <textarea class="form-control @error('body') is-invalid @enderror"
                                          wire:model.defer="body" rows="4" placeholder="Write your comment..."
                                          style="font-size: 16px; padding: 12px; border-radius: 8px;"></textarea>
                                @error('body') <div class="invalid-feedback" style="font-size: 0.9rem;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-xxl-12 mt-3">
                            <div class="postbox__comment-btn">
                                <button type="submit" class="primary-btn-1 btn-hover" style="font-size: 1rem; padding: 10px 28px;">
                                    {{ $editingCommentId ? 'Update Comment' : 'Post Comment' }}
                                    <span style="top: 147.172px; left: 108.5px;"></span>
                                </button>
                                @if($editingCommentId)
                                    <button type="button" class="btn btn-secondary ms-2" wire:click="cancelEdit" style="font-size: 1rem;">Cancel</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            @if(!$submitted)
                <div class="postbox__comment-form mt-50">
                    <h5 class="mb-20" style="font-size: 1.2rem;">Leave a Comment</h5>
                    <form wire:submit.prevent="submit">
                        <div style="position:absolute;left:-9999px;">
                            <label for="honeypot">Leave empty</label>
                            <input type="text" id="honeypot" wire:model="honeypot" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="postbox__comment-input">
                                    <label style="font-weight: 600; font-size: 1rem;">Your Name *</label>
                                    <input type="text" class="form-control @error('guestName') is-invalid @enderror"
                                           wire:model.defer="guestName" placeholder="Your name"
                                           style="font-size: 16px; padding: 12px; border-radius: 8px;">
                                    @error('guestName') <div class="invalid-feedback" style="font-size: 0.9rem;">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="postbox__comment-input">
                                    <label style="font-weight: 600; font-size: 1rem;">Your Email *</label>
                                    <input type="email" class="form-control @error('guestEmail') is-invalid @enderror"
                                           wire:model.defer="guestEmail" placeholder="you@example.com"
                                           style="font-size: 16px; padding: 12px; border-radius: 8px;">
                                    @error('guestEmail') <div class="invalid-feedback" style="font-size: 0.9rem;">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div class="postbox__comment-input">
                                    <label style="font-weight: 600; font-size: 1rem;">Comment *</label>
                                    <textarea class="form-control @error('body') is-invalid @enderror"
                                              wire:model.defer="body" rows="4" placeholder="Write your comment..."
                                              style="font-size: 16px; padding: 12px; border-radius: 8px;"></textarea>
                                    @error('body') <div class="invalid-feedback" style="font-size: 0.9rem;">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-xxl-12 mt-3">
                                <div class="postbox__comment-btn">
                                    <button type="submit" class="primary-btn-1 btn-hover" style="font-size: 1rem; padding: 10px 28px;">Post Comment</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <p class="text-muted small mt-3" style="font-size: 0.9rem;"><i class="fal fa-info-circle"></i> You'll receive a verification email to confirm your comment.</p>
                </div>
            @else
                <div class="alert alert-success mt-4" style="font-size: 1rem;">
                    <i class="fas fa-check-circle"></i> Please check your email to verify your comment. It will appear once confirmed.
                    <button class="btn btn-link p-0 ms-3" wire:click="$set('submitted', false)" style="font-size: 1rem;">Post another comment</button>
                </div>
            @endif
        @endauth
    @else
        <div class="alert alert-info mt-4" style="font-size: 1rem;">
            <i class="fas fa-info-circle me-2"></i> Comments are closed for this post.
        </div>
    @endif
</div>

<style>
    .postbox__comment-form textarea.form-control,
    .postbox__comment-form input.form-control,
    .reply-form-inline textarea.form-control,
    .reply-form-inline input.form-control,
    .comment-body textarea.form-control {
        font-size: 16px !important;
        line-height: 1.6 !important;
    }
</style>