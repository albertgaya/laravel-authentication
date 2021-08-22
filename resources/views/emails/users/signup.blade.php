@component('mail::message')
# Hi {{$user->name}}

Use below pin to verify your account.

@component('mail::panel')
{{$user->email_verification_token}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
