@component('mail::message')
# Reset Your Password

Hello,

You are receiving this email because we received a password reset request for your account.

Your password reset token is:

**{{ $token }}**

Please copy this token and use it to reset your password in the application.

If you did not request a password reset, no further action is required.

Thanks,
{{ config('app.name') }}
@endcomponent
