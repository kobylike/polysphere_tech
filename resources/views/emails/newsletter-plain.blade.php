{{ $subject }}
===========================================

{!! $body !!}

---
You received this email because you subscribed to our newsletter.
To unsubscribe, visit:
{{ route('newsletter.unsubscribe', ['email' => $subscriber->email, 'token' => $subscriber->verification_token]) }}

&copy; {{ date('Y') }} Polysphere Tech. All rights reserved.