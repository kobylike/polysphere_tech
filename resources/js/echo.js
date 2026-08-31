window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.REVERB_APP_KEY,
    wsHost: window.REVERB_HOST,
    wsPort: window.REVERB_PORT ?? 80,
    wssPort: window.REVERB_PORT ?? 443,
    forceTLS: window.REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
    },
});

window.dispatchEvent(new Event('echo-ready'));