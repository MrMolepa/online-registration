@component('mail::message')
# Confirmation
{{$user->first_name}}{{" "}} {{$user->last_name}}, this confirms that we’ve just received your online payment.<br>
 Thank you for  using our e-services application.
@component('mail::panel')
{{$service->description}}  -- {{date("Y-m-d")}}
@endcomponent
@component('mail::table')
| Reference Number      | Service Name         | Amount |
| ------------- |:-------------:| --------:|
| {{$reference_number}}     |   {{$service->description}}      | LSL {{number_format((float)$service->price, 2, '.', '')}}        |
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
