@component('mail::message')
<div style="text-align: center;">
<img src="data:image/jpg;base64,{{base64_encode(file_get_contents(public_path('/assets/images/mailheader.jpg')))}}">
</div>
Dear {{ $other_names }}{{ ' ' }} {{ $surname }}
@component('mail::subcopy')
Examinations Council of Lesotho would like to invite
you to sign this Contract Form to invigilation at {{ $center_name }}  ({{ $center_no }})
@endcomponent

Please respond to this email to confirm your attendance by the clicking below button and completing the Contract Form.
@component('mail::button', ['url' => $url])
Accept
@endcomponent

If you are having trouble clicking the "Accept" button, click or copy and paste the URL below into your web browser:
*[{{Url($url)}}]({{Url($url)}})*
To reject this Offer click <a href="{{ $declined }}" style="color: red; text-decoration: none;">decline</a>.

<div style="text-align: center;">
<img src="data:image/jpg;base64,{{base64_encode(file_get_contents(public_path('/assets/images/footer.jpg')))}}">
</div>

{{ config('app.name') }}
@endcomponent
