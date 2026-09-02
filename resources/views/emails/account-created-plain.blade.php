===========================================
WELCOME TO {{ $companyName }}
===========================================

Hello {{ $user->name }},

@if($createdByName)
    {{ $createdByName }} has created an account for you on {{ $companyName }}.
@else
    An account has been created for you on {{ $companyName }}.
@endif
You've been assigned the role: {{ $roleName }}@if($positionName), as {{ $positionName }}@endif.

Your account details:
-------------------------------------------
Email : {{ $user->email }}
Temporary Password: {{ $temporaryPassword }}
-------------------------------------------

⚠️ For your security, please sign in and change this password immediately.

Sign in here:
{{ $loginUrl }}

If you weren't expecting this account, please contact your administrator.

---
{{ $companyName }}
123 Tech Hub, Innovation District, Silicon Valley, CA 94025
© {{ date('Y') }} {{ $companyName }}