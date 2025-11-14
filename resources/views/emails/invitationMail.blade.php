@component('mail::message')
{{-- Header --}}
# Examinations Council of Lesotho
---
Dear **{{ $recipient->first_name }} {{ $recipient->last_name }}**,

You are invited to serve as a **{{ $invitation->role->name }}** for the upcoming examination session.

@component('mail::panel')
📌 **Session:** {{ $invitation->session }}
📌 **Financial Year:** {{ $invitation->financial_year }}
@endcomponent

Please submit your response using the secure link below.
Confirm your attendance by clicking the button and completing the Contract Form.

@component('mail::button', ['url' => $url, 'color' => 'success'])
✅ Accept Invitation
@endcomponent
---
Thanks,
**{{ config('app.name') }}**

@component('mail::subcopy')
This is an automated invitation from the Examinations Council of Lesotho.
If you did not expect this, please ignore the email.
@endcomponent
@endcomponent
