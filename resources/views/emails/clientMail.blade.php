@component('mail::message')
# {{$client->name}}
{{$message}}

@component('mail::panel')
Reference Number  {{$client->reference_no}}
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
