===========================================
TWO-FACTOR AUTHENTICATION DISABLED
===========================================

Hi {{ $user->name }},

Two-factor authentication has been DISABLED on your {{ $companyName }} account.
Your account is now protected only by your password.

Details of this change:
-------------------------------------------
When : {{ $changedAt }}
IP Address : {{ $ipAddress }}
Device : {{ $userAgent }}
-------------------------------------------

⚠️ Didn't do this? Your account may be compromised.
Reset your password immediately and re-enable 2FA:
{{ $securityUrl }}

Need help? Contact our security team right away.

---
{{ $companyName }}
123 Tech Hub, Innovation District, Silicon Valley, CA 94025
© {{ date('Y') }} {{ $companyName }}