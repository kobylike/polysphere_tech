<li class="nav-item dropdown notification_dropdown">
    <a class="nav-link bell-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"
                stroke="white" stroke-linecap="round" stroke-linejoin="round" />
            <path
                d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21"
                stroke="white" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @if($unreadCount > 0)
            <span class="dz-msg-badge dz-msg-badge-pop">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end notif-dropdown-panel">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h6 class="mb-0">Notifications</h6>
            @if($unreadCount > 0)
                <button class="btn btn-sm btn-link text-primary" wire:click="markAllRead">
                    Mark all as read
                </button>
            @endif
        </div>

        @if(count($notifications) > 0)
            <div class="dz-scroll style-1 p-3">
                @foreach($notifications as $notif)
                    <div class="d-flex align-items-start gap-2 mb-3 pb-3 border-bottom">
                        <div class="flex-shrink-0">
                            <i class="fas {{ $notif['icon'] ?? 'fa-circle-info' }} text-{{ $notif['type'] ?? 'info' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $notif['title'] ?? 'Notification' }}</strong>
                                <small class="text-muted">{{ $notif['created_at'] }}</small>
                            </div>
                            <p class="mb-0 small text-muted">{{ $notif['body'] }}</p>
                            @if($notif['link'])
                                <a href="{{ $notif['link'] }}" class="small text-primary" wire:navigate>View</a>
                            @endif
                            @if(!$notif['is_read'])
                                <button class="btn btn-sm btn-link p-0 ms-2" wire:click="markAsRead({{ $notif['id'] }})">
                                    Mark as read
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($limit < $unreadCount + count($notifications))
                    <div class="text-center mt-2">
                        <button class="btn btn-sm btn-outline-primary" wire:click="loadMore">
                            Load more
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-bell-slash text-muted mb-2 d-block"></i>
                <p class="text-muted small">No notifications</p>
            </div>
        @endif
    </div>
</li>