NEW CONTACT FORM MESSAGE — POLYSPHERE TECH
===========================================

Category : {{ $category }}
Subject : {{ $messageSubject }}
From : {{ $senderName }} <{{ $senderEmail }}>
    Received : {{ $sentAt }}

    -------------------------------------------
    {{ $messageBody }}
    -------------------------------------------

    Reply directly to: {{ $senderEmail }}

    Submitted via the contact form at {{ config('app.url') }}/contact

    © {{ date('Y') }} Polysphere Tech · 123 Tech Hub, Innovation District, Silicon Valley, CA 94025