@component('mail::message')

# Registration | ({{$candidate->national_id}}) {{$candidate->candidate_surname}}{{" "}} {{$candidate->candidate_other_name}}
 This confirms that we have received your online payment as shown below:<br>
@component('mail::panel')
Registered at {{$candidate->center_no}}  -- {{date("Y-m-d")}}
@endcomponent
@component('mail::table')
| Examiantion Level & Session | Total Subjects | Amount |
| ------------- |:-------------:| --------:|
| {{$candidate->level}} - {{$candidate->session}} {{date("Y")}} | {{$candidate->subject_number}}      | LSL {{number_format((float) $amount, 2, '.', '')}}        |
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
