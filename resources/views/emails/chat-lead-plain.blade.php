NEW CHAT LEAD — POLYSPHERE TECH
===========================================

Captured : {{ $sentAt }}
Source : {{ $lead->source }}
From : {{ $lead->name ?: 'Chat visitor' }} <{{ $lead->email }}>
    @if($lead->phone)
        Phone : {{ $lead->phone }}
    @endif
    @if($lead->company)
        Company : {{ $lead->company }}
    @endif
    Page : {{ $pageUrl }}
    @if($lead->ip_address)
        IP : {{ $lead->ip_address }}
    @endif

    -------------------------------------------
    {{ $lead->message }}
    -------------------------------------------

    @if(count($recentTurns) > 0)
        RECENT CONVERSATION
        -------------------------------------------
        @foreach($recentTurns as $turn)
            {{ ($turn['role'] ?? '') === 'user' ? 'Visitor' : 'Sphere' }}: {{ $turn['content'] ?? '' }}

        @endforeach
        -------------------------------------------
    @endif

    Reply directly to: {{ $lead->email }}

    Captured by the Sphere chat widget on {{ config('app.url') }}

    © {{ date('Y') }} Polysphere Tech · Accra, Ghana