===========================================
YOU'RE INVITED TO JOIN {{ $companyName }}
===========================================

Hello,

{{ $invitedByName }} has invited you to join {{ $companyName }}.
You will be assigned the role: {{ $roleName }}.

Your invitation details:
-------------------------------------------
Email : {{ $invitation->email }}
Expiry: {{ $expiryDate }}
-------------------------------------------

To accept this invitation and create your account, click the link below:

{{ $registrationUrl }}

If you didn't expect this invitation, you can safely ignore this message.

---
{{ $companyName }}
123 Tech Hub, Innovation District, Silicon Valley, CA 94025
© {{ date('Y') }} {{ $companyName }}