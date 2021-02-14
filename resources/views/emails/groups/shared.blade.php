@component('mail::message')

{{ $sharedBy->name . " ($sharedBy->email)" }} shared a password group with you.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
