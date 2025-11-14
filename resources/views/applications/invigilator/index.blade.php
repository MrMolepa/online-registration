@extends('layouts.application')
@section('content')
    <div class="container" id="main-application">
        <div class="header">
            <p id="title" class="text-center">Contract of service between</p>
            <div id="description" class="text-center" value="{{ $invigilator->surname }}">
                <h1> Examinations Council of Lesotho </h1> and
                <div name="name" id="name" class="text-center header_b">
                    {{ $invigilator->other_names }} {{ $invigilator->surname }}
                </div>
            </div>
        </div>
        <div class="form-wrap">
            <form id="application-edit-form" method="POST" action="{{ $url }}">
                @csrf
                @method('PUT')
                <h6 class="text-center">PERSONAL INFORMATION</h6>
                <div class="container px-md-0">
                    <div class="sec-title text-center">
                        <span class="decor">
                            <span class="inner">
                            </span>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">National ID</label>
                            <input readonly type="text" value="{{ $invigilator->national_id }}" name="national_id"
                                id="national_id" placeholder="ID number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Invigilator type</label>
                            <input readonly type="text"
                                value="{{ $invigilator->invigilation_role->invigilation_type->name }}"
                                name="invigilation_role_id" id="invigilation_role_id" placeholder="Enter your name"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Surname</label>
                            <input readonly type="text" value="{{ $invigilator->surname }}" name="surname" id="surname"
                                placeholder="Enter your name" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Other Names</label>
                            <input readonly type="text" value="{{ $invigilator->other_names }}" name="other_names"
                                id="other_names" placeholder="Enter your name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="text-label" for="date_of_birth">Date of Birth <span class="text-danger">*</span>
                            </label>
                            <input type="date" value="{{ $invigilator->date_of_birth }}" name="date_of_birth"
                                id="date_of_birth" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label>Gender <span class="text-danger">*</span> </label>
                            <select id="gender" name="gender" class="form-control">
                                <option selected value>Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Phone Number</label>
                            <input readonly type="text" value="{{ $invigilator->phone_number }}" name="phone_number"
                                id="phone_number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Email</label>
                            <input readonly type="email" value="{{ $invigilator->email }}" name="email" id="email"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label id="name-label" for="name"> Highest Qualification <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="text" value="{{ $invigilator->qualification }}" name="qualification"
                                id="qualification" class="form-control" placeholder="Highest Academic certificate">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label id="name-label" for="district">District</label>
                            <select class="form-control" name="district_id" id="district_id">
                                <option value="">Select distrct</option>
                                @foreach ($invigilator_districts as $invigilator_district)
                                    <option value="{{ $invigilator_district->district_code }}">
                                        {{ $invigilator_district->district_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label id="name-label" for="village">Village</label>
                            <input type="text" placeholder="Enter your village" name="village" id="village"
                                class="form-control">
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="text-center">PAYMENT METHOD</h6>
                <div class="container px-md-0">
                    <div class="sec-title text-center">
                        <span class="decor">
                            <span class="inner">
                            </span>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form group">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-0" role="tab">Payment
                                    rules</a>
                            </li>

                        </ul><!-- Tab panes -->
                        <br>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-0" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-7">
                                        <p>You will be paid: <STRONG>M
                                                {{ $invigilator->invigilation_role->invigilator_paymentamount->amount }}.00</STRONG>
                                            for
                                            temporary
                                            job position:
                                            <strong>{{ $invigilator->invigilation_role->invigilation_type->name }}</strong>
                                            <li>Please note that payments will be done on registered mobile number.
                                            </li>
                                            <li>The name that you have used to register your mobile/account number
                                                should be
                                                the
                                                same as the one on the contract.</li>

                                        <p><STRONG>NB:</STRONG> Choose your suitable payment method by clicking on
                                            payment
                                            method
                                            mentioned.</p>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group  col-12">
                                            <label for="invigilation_type">Select your payment
                                                method</label>
                                            <select class="form-control" id="paymentmethods" name="payment_id">
                                                <option selected value="">Select Payment Method</option>
                                                @foreach ($payment_methods as $payment_method)
                                                    <option value="{{ $payment_method->id }}">
                                                        {{ $payment_method->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="paymentmethods-attributes">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="text-center">DECLARATION</h6>
                <div class="container px-md-0">
                    <div class="sec-title text-center">
                        <span class="decor">
                            <span class="inner">
                            </span>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <p> I declare that:
                        <li> I have no personal interest whatever in the examinations that I will be invigilating.
                        </li>
                        <li>I do not have any health problems that can affect my performance.</li>
                        <li>I have not been linked directly or indirectly to any malpractice in
                            examinations.
                        </li>
                    </p>
                    <div class="row">
                        <input type="checkbox" name="terms" id="terms" />
                        <label class="col-sm-11"> I agree with terms and
                            conditions </label>
                    </div>
                </div>
                <div class="col-md-12" id="errors">
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <button type="submit" id="submit-application" name="add-appliation" class="btn btn-sm"
                            disabled><i class="fas fa-spinner fa-spin loadingSpinnersave"
                                hidden></i><span>Submit</span></button>
                    </div>

                </div>

            </form>
        </div>
    </div>
    {{-- success --}}
    <div class="success-wrapper">
        <div class="wrapperAlert">
            <div class="contentAlert">
                <div class="topHalf">

                    <p><svg viewBox="0 0 512 512" width="100" title="check-circle">
                            <path
                                d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
                        </svg></p>
                    <h1>Congratulations</h1>

                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>

                <div class="bottomHalf">

                    <p>You have successfully signed a contract.

                    <div name="name" id="name" class="text-center">
                        <b>{{ $invigilator->other_names }} {{ $invigilator->surname }}</b>
                    </div>

                    <div class="container px-md-0">
                        <div class="sec-title text-center">
                            <span class="decor">
                                <span class="inner">
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="btn-toolbar button_downloads" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group mr-2 no_underline" role="group" aria-label="First group">
                            <a class="btn no_underline" href="{{ $contracturl }}" data-toggle="tooltip" data-url=""
                                data-original-title="download" class="download-pdf btn-sm fa fa-download"
                                style="text-decoration: none">Signed
                                Contract</a>
                        </div>
                        <div class="btn-group mr-2 no_underline" role="group" aria-label="Second group">
                            <a class="btn no_underline" href="{{ asset('assets/pdf/Invigilator_handout.pdf') }}"
                                style="text-decoration: none">Handbook</a>
                        </div>
                    </div>

                    <b>NB:</b><i>* Click above buttons to download. *</i>
                    </p>
                </div>

            </div>

        </div>
    </div>
    <button id="toTopBtn" title="Go to top" class="btn btn-primary">
        <i class="fa fa-arrow-up"></i>
    </button>

    <!-- partial -->

    @push('scripts')
        <script type="text/javascript">
            //update
            $(document).ready(function() {
                $(window).scroll(function() {
                    if ($(this).scrollTop() > 20) {
                        $("#toTopBtn").fadeIn();
                    } else {
                        $("#toTopBtn").fadeOut();
                    }
                });

                $("#toTopBtn").click(function() {
                    $("html, body").animate({
                        scrollTop: 0
                    }, 0);
                    return false;
                });

                function initailizeIntlTelInput() {
                    var phone = document.querySelector("#payable_phone_number");
                    var iti = window.intlTelInput(phone, {
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/utils.js",
                        initialCountry: "LS",
                        geoIpLookup: function(callback) {
                            var elt = document.getElementById('payable_phone_number'),
                                current_value = elt.value;
                            elt.value = ''; // unset the value before checking geoip
                            $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "";
                                callback(countryCode);
                                setTimeout(function() {
                                    elt.value =
                                        current_value; // set value back after geoip is done.
                                }, 10);
                            });
                        },

                    });

                    // store the instance variable so we can access it in the console e.g. window.iti.getNumber()
                    window.iti = iti;

                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // Update
                $(document).on('click', '#submit-application', function(e) {
                    e.preventDefault();
                    var editForm = $("#application-edit-form");
                    var url = editForm.attr('action');
                    var captionsave = $('span', this).html();
                    var $button = $(this);
                    $button.prop('disabled', true);
                    var i = 0;
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: editForm.serializeArray(),
                        beforeSend: function() {
                            $(".loadingSpinnersave").removeClass('hidden');
                            $button.find("span").html('Saving..');
                            $button.prop('disabled', true);
                            i++;
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $('#main-application').hide();
                                $('.success-wrapper').css({
                                    "display": "flex"
                                });
                            } else {
                                var errorshtml='<ul>'
                                $.each( data.errors, function(key, errors) {
                                    for (const error in errors) {
                                        const value = errors[error];
                                        errorshtml +=`<li><span>${value}</span></li>`
                                    }
                                });
                                  errorshtml +='</ul>';
                                $("#errors").html(
                                `<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    ${errorshtml}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>` );






                                console.log(errorshtml);
                                //printErrorMsg('#main-application', data.errors);
                            }

                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $(".loadingSpinnersave").addClass('hidden');
                        },
                        complete: function() {
                            i--;
                            if (i <= 0) {
                                $(".loadingSpinner").addClass('hidden');
                                $button.find("span").html(captionsave);
                                $button.prop('disabled', false);
                            }
                        },
                    });
                });
                //payement-methods
                $(document).on('change', '#paymentmethods', function(e) {

                    var id = $(this).val();
                    $.ajax({
                        type: "GET",
                        url: '{{ $geturl }}',
                        data: {
                            payment_method_id: id
                        },
                        success: function(data) {

                            var payment_method_attributes = data.payment_methods;
                            $("#paymentmethods-attributes").html(payment_method_attributes);
                            initailizeIntlTelInput();
                        },
                        error: function(data) {
                            console.log("error", data);
                        }
                    });

                });
                // Disable submit button
                $('#terms').change(function() {
                    handleDisable(this);
                });

                function handleDisable(elm) {
                    $('#submit-application').attr('disabled', !elm.checked);
                }
            });
            /****  Print errors*******/
            function printErrorMsg(parent, msg) {
                $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                    $(`${parent} .invalid-feedback`).remove();
                    $(`${parent} .is-invalid`).removeClass('is-invalid');
                    // console.log(input.attr('type') + 'Name: ' + input.attr('name') + '  Value: ' + input.val());
                });
                $.each(msg, function(key, errors) {
                    for (const error in errors) {
                        const value = errors[error];
                        $(`[name='${key}']`).addClass('is-invalid');
                        $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                            `${parent} [name='${key}']`)
                    }
                });
            }
        </script>
    @endpush
@endsection
