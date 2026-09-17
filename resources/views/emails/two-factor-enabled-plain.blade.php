===========================================
TWO-FACTOR AUTHENTICATION ENABLED
===========================================

Hi {{ $user->name }},

Two-factor authentication is now ON for your {{ $companyName }} account.
You'll be asked for a 6-digit code from your authenticator app on your next sign-in.

Details of this change:
-------------------------------------------
When : {{ $changedAt }}
IP Address : {{ $ipAddress }}
Device : {{ $userAgent }}
-------------------------------------------

🔒 Store your recovery codes somewhere safe:
{{ $securityUrl }}

Didn't enable 2FA? Contact support immediately — your account may be at risk.

---
{{ $companyName }}
Accra, Ghana
© {{ date('Y') }} {{ $companyName }}