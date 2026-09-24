POLYSPHERE TECH — WE GOT YOUR MESSAGE
===========================================

@if($firstName)
    Hi {{ $firstName }},
@else
    Hi there,
@endif

Thanks for getting in touch with Polysphere Tech. We've received your
message and a member of our team will get back to you within 24 hours.

In the meantime, feel free to reply to this email with anything else
that might help us prepare — additional context, timelines, or questions.

-------------------------------------------
WHAT HAPPENS NEXT
-------------------------------------------

1. We review your message
A member of our team looks at what you've shared and prepares a
thoughtful reply.

2. We reach out personally
You'll hear from us within 24 hours — usually much sooner during
business hours.

3. We talk about your project
A short discovery call or email exchange to understand what you
need and how we can help.

-------------------------------------------
NEED TO REACH US SOONER?
-------------------------------------------
Call us on {{ $contactPhone }}
Or reply directly to this email.

Talk soon,
The Polysphere Tech Team

-------------------------------------------
Message received {{ $receivedAt }}
@if($lead->email)
    From: {{ $lead->email }}
@endif

Polysphere Tech · Accra, Ghana
{{ $siteUrl }}