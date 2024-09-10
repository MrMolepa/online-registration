@component('mail::message')
    # Invigilator contract invitations
    {{ $other_names }}{{ ' ' }} {{ $surname }} , You are invited to invigilate at {{ ' ' }}
    {{ $center_no }}.
    Click the link below to complete the contract form.<br>
    <a href="{{ $url }}">{{ $url }}</a>
    Thanks
    {{ config('app.name') }}
@endcomponent



@component('mail::message')
<h2>Hello {{$body['name']}},</h2>
<p>The email is a sample email for Laravel Tutorial: How to Send an Email using Laravel 8 from @component('mail::button', ['url' => $body['url_a']])
Bacancy Technology
@endcomponent</p>

<p>Visit @component('mail::button', ['url' => $body['url_b']])
Laravel Tutorials
@endcomponent and learn more about the Laravel framework.</p>


Happy coding!<br>

Thanks,<br>
{{ config('app.name') }}<br>
Laravel Team.
@endcomponent
