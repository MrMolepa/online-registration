@component('mail::message')
# Examinations Council of Lesotho | Invigilator Contract Form
Hello, {{ $other_names }}{{ ' ' }} {{ $surname }} , You are invited to invigilate at center:
{{ ' ' }}
{{ $center_no }}.


Click the link below to complete the contract form.
@component('mail::button', ['url' => $url])
Invigilation Contract form
@endcomponent
Thanks
{{ config('app.name') }}
@endcomponent
