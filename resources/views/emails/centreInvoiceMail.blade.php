@component('mail::message')
# Invoice
Dear {{$center->center_no}}  {{$center->center_name}}
This is to confirm  that your proof of payment with  Examinations Council of Lesotho online Registration
was successfully for LSL{{number_format((float)$amount_paid, 2, '.', '')}}.
@component('mail::panel')
Fees  -- {{date("Y-m-d")}}
@endcomponent
@component('mail::table')
|Description   | Amount         |
| ------------- |:-------------:|
| Total          | LSL {{number_format((float)$schoolfees['sponsor'][2], 2, '.', '')}}|
| Total Charges  | LSL {{number_format((float)$schoolfees['total_charge'], 2, '.', '')}}|
| Total Paid  | LSL {{number_format((float)$total_paid, 2, '.', '')}}|
| Balance  | LSL {{number_format((float)$balance, 2, '.', '')}}|
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
