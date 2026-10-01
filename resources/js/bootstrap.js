import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});

/**
 * FIX: without this, every broadcast event your own actions trigger
 * (e.g. saving your profile) gets echoed straight back to your own
 * browser over the socket, which then fires a SECOND Livewire request
 * (AccountSettings::syncProfile(), navbar updates, etc.) landing almost
 * simultaneously with your save's own response — a self-collision that
 * was intermittently causing 403s on the very save you just made.
 *
 * ->toOthers() on the backend (see ProfileHelper::broadcast()) tells
 * Laravel to skip the triggering connection — but it can only do that
 * if it knows which connection triggered the request. This attaches
 * the current socket ID to every single Livewire request automatically,
 * the same way Laravel's docs show doing it for normal Axios requests,
 * just hooked into Livewire's own request lifecycle instead since
 * Livewire doesn't send its requests through window.axios.
 */
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ options }) => {
        if (window.Echo && typeof window.Echo.socketId === 'function') {
            const socketId = window.Echo.socketId();
            if (socketId) {
                options.headers['X-Socket-ID'] = socketId;
            }
        }
    });
});