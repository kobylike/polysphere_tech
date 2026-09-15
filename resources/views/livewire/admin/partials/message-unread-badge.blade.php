<span id="dz-msg-unread-badge" class="dz-msg-badge" style="{{ $count > 0 ? '' : 'display:none;' }}"
    wire:key="msg-unread-badge">
    {{ $count > 99 ? '99+' : $count }}
</span>