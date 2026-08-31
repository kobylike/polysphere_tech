<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ICE Servers (STUN / TURN)
    |--------------------------------------------------------------------------
    |
    | STUN servers are free and work for most public IPs.
    | For production behind strict firewalls, deploy coturn and add TURN URLs.
    |
    */
    'ice_servers' => [
        [
            'urls' => [
                'stun:stun.l.google.com:19302',
                'stun:stun1.l.google.com:19302',
                'stun:stun2.l.google.com:19302',
            ],
        ],
        // Example TURN (uncomment when you have your own coturn server)
        // [
        //     'urls' => 'turn:your-turn-server.com:3478',
        //     'username' => env('TURN_USERNAME'),
        //     'credential' => env('TURN_PASSWORD'),
        // ],
    ],
];
