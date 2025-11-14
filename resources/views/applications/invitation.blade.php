@extends('layouts.application')
@section('content')
    <div class="container" id="main-application">
        <div class="header text-center">
            <p id="title">Contract of service between</p>
            <h1>Examinations Council of Lesotho</h1> and
            <div id="name" class="header_b">{{ $invitation->recipient->first_name }}
                {{ $invitation->recipient->last_name }}</div>
        </div>

        <div class="form-wrap">
            <div class="step-header d-flex justify-content-between mb-4">
                <div class="step-item active">1.Personal Info</div>
                <div class="step-item">2. Bank Details </div>
                <div class="step-item">3. Contract</div>
            </div>
            <form id="application-edit-form" method="POST" action="{{ $url }}">
                @csrf
                <input type="hidden" name="status" id="status" value="{{ $invitation->status }}">
                <!-- STEP 1: PERSONAL INFORMATION -->
                <div class="form-step active">
                    <!-- your personal info fields go here -->
                    <h6 class="text-center">PERSONAL INFORMATION (update)</h6>
                    <div class="container">
                        <div class="sec-title text-center"><span class="decor"><span class="inner"></span></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4  col-sm-12">
                            <!-- Already filled fields -->
                            @if (count($responses))
                                <h4 class="mb-2">Invitation Details: <strong>{{ $invitation->role->name }}</strong>
                                </h4>
                                <ul class="list-group">
                                    @foreach ($allFields as $field)
                                        @if (isset($responses[$field->id]))
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $field->label }}
                                                <span class="badge bg-success">{{ $responses[$field->id] }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                        <div class="col-lg-8  col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="national_id">National ID</label>
                                    <input type="text" value="{{ $invitation->recipient->national_id }}"
                                        name="national_id" id="national_id" placeholder="National ID" class="form-control">
                                </div>
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="first_name">First Name</label>
                                    <input readonly type="text" value="{{ $invitation->recipient->first_name }}"
                                        name="first_name" id="first_name" class="form-control">
                                </div>
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="last_name">Last Name</label>
                                    <input readonly type="text" value="{{ $invitation->recipient->last_name }}"
                                        name="last_name" id="last_name" class="form-control">
                                </div>
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="email">Email</label>
                                    <input readonly type="text" value="{{ $invitation->recipient->email }}"
                                        name="email" id="email" class="form-control">
                                </div>

                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="phone_number">Phone Number</label>
                                    <input readonly type="text" value="{{ $invitation->recipient->phone_number }}"
                                        name="phone_number" id="phone_number" class="form-control">
                                </div>
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="village">Village</label>
                                    <input type="text" value="{{ $invitation->recipient->village }}" name="village"
                                        id="village" class="form-control">
                                </div>
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label id="district" for="district">District</label>
                                    <select class="form-control" name="district" id="district">
                                        <option value="">Select distrct </option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->district_name }}"
                                                @if (old('district', trim($invitation->recipient->district)) == trim($district->district_name)) selected @endif>
                                                {{ $district->district_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach ($allFields as $field)
                                    @if (!isset($responses[$field->id]))
                                        <div class="form-group col-lg-6 col-sm-12">
                                            <label>{{ $field->label }} @if ($field->required)
                                                    *
                                                @endif
                                            </label>
                                            @if ($field->type == 'number')
                                                <input type="number" name="fields[{{ $field->name }}]"
                                                    class="form-control">
                                            @elseif ($field->type == 'text')
                                                <input type="text" name=" fields[{{ $field->name }}]"
                                                    class="form-control">
                                            @elseif ($field->type == 'date')
                                                <input type="date" name=" fields[{{ $field->name }}]"
                                                    class="form-control">
                                            @elseif ($field->type == 'file')
                                                <input type="file" name="fields[{{ $field->name }}]"
                                                    class="form-control">
                                            @elseif ($field->type == 'select')
                                                <select name="fields[{{ $field->name }}]" class="form-control">
                                                    <option value="">Select {{ $field->label }}</option>
                                                    @foreach ($field->options as $key => $opt)
                                                        <option value="{{ $key }}">{{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>


                        </div>
                    </div>
                    <div class="d-flex justify-content-end flex-wrap">
                        <button type="button" class="btn btn-primary action-btn next-step">Next</button>
                    </div>
                </div>
                <!-- STEP 2: PAYMENT -->
                <div class="form-step">
                    <!-- PAYMENT METHOD -->
                    <h6 class="text-center">Bank Details</h6>
                    <div class="container px-md-0">
                        <div class="sec-title text-center"><span class="decor"><span class="inner"></span></span></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="paymentmethods">Select your payment method</label>
                                <select class="form-control" id="paymentmethods" name="payment_id">
                                    <option selected value="">Select Payment Method</option>
                                    @foreach ($payment_methods as $payment_method)
                                        <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="paymentmethods-attributes"></div>
                    <div class="d-flex justify-content-end flex-wrap">
                        <button type="button" class="btn btn-secondary action-btn prev-step">Previous</button>
                        <button type="button" class="btn btn-primary action-btn next-step">Next</button>
                    </div>

                </div>
                <!-- STEP 3: CONTRACT -->
                <div class="form-step">
                    <!-- DECLARATION -->
                    <h6 class="text-center">CONTRACT</h6>
                    <div class="container px-md-0">
                        <div class="sec-title text-center"><span class="decor"><span class="inner"></span></span></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-sm-12">
                            <label for="principal_first_name">Principal First Name</label>
                            <input type="text"
                                value="{{ optional($invitation->recipient->center->principal)->first_name }}"
                                name="principal_first_name" id="principal_first_name" class="form-control">
                        </div>
                        <div class="form-group col-lg-6 col-sm-12">
                            <label for="principal_last_name">Principal Last Name</label>
                            <input type="text"
                                value="{{ optional($invitation->recipient->center->principal)->last_name }} "
                                placeholder="" name="principal_last_name" id="principal_last_name" class="form-control">
                        </div>
                        <div class="form-group col-lg-6 col-sm-12">
                            <label for="principal_email">Principal Email</label>
                            <input type="text"
                                value="{{ optional($invitation->recipient->center->principal)->email }}"
                                name="principal_email" id="principal_email" class="form-control">
                        </div>
                        <div class="form-group col-lg-6 col-sm-12">
                            <label for="principal_phone_number">Principal Phone Number</label>
                            <input type="text"
                                value="{{ optional($invitation->recipient->center->principal)->phone_number }}"
                                name="principal_phone_number" id="principal_phone_number" class="form-control">
                        </div>


                        {{-- <div class="signature-area" id="signatureArea">
                            <p class="signature-placeholder">Click here to Upload Signed Document</p>
                            <!-- Hidden file input -->
                            <input type="file" name="signed_document" id="signatureUpload"
                                accept="image/jpeg,application/pdf" hidden>
                            <i class="fas fa-upload"></i> Upload Signed Document (PDF/Image)
                            <!-- Preview signature -->
                            <div class="signature-preview"></div>
                        </div> --}}

                    </div>
                    <h6>I declare that:</h6>
                    <ul>
                        <li>The information that I have provided above is correct</li>
                        <li>I have been trained in the duties that he/she is about to undertake.</li>
                        <li>I have no personal interest whatever in the examinations center/s that I will be
                            rendering services.
                        </li>
                        <li>I do not have any health problems that can affect my performance.</li>
                        <li>I have not been linked directly or indirectly to any malpractice in examinations.</li>
                        <li>I have been officially released by the employer in order to provide his/her services to ECoL.
                        </li>
                    </ul>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1">
                        <label class="form-check-label" for="terms">I agree with terms and
                            conditions</label>
                    </div>

                    <div class="d-flex justify-content-end flex-wrap">
                        <button type="button" class="btn btn-secondary action-btn prev-step">Previous</button>
                        <button type="button" id="submit-application"
                            class="btn btn-primary action-btn next-step">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SUCCESS WRAPPER -->
    <div class="success-wrapper" style="display:none;">
        <div class="wrapperAlert">
            <div class="contentAlert text-center">
                <div class="topHalf">
                    <p>
                        <svg viewBox="0 0 512 512" width="50" title="check-circle">
                            <path
                                d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
                        </svg>
                    </p>
                    <h1>Congratulations</h1>
                </div>
                <div class="bottomHalf">
                    <p>Your application has been forwarded to your principal for approval.</p>
                    @if (!$downloadable)
                        <div class="btn-toolbar button_downloads">
                            <div class="btn-group mr-2">
                                <section class="action">
                                    <p>Please download and confirm before <span class="deadline">:
                                            {{ $invitation->end_date ? date('F j, Y', strtotime($invitation->end_date)) : '[Date not avaliable]' }}</span>.
                                    </p>
                                    <div class="buttons">
                                        <a href="{{ $contractUrl }}" class="btn btn-secondary">
                                            <i class="fas fa-file-contract"></i>Download Contract
                                        </a>
                                    </div>
                                </section>
                            </div>
                        </div>
                        <b>NB:</b><i>* Click above buttons to download. *</i>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <button id="toTopBtn" title="Go to top" class="btn btn-primary">
        <i class="fa fa-arrow-up"></i>
    </button>

    @push('scripts')
        <script>
            $(document).ready(function() {
                completedContract();
                let currentStep = 0;
                const steps = $(".form-step");
                $('#paymentmethods').trigger('change');
                $(".next-step").click(function() {
                    let form = $("#application-edit-form")[0];
                    let formData = new FormData(form);
                    formData.append("step", currentStep + 1);

                    saveStep(currentStep + 1, formData, function() {
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            showStep(currentStep);
                        }
                    });
                });

                $(".prev-step").click(function() {
                    if (currentStep > 0) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });

                function showStep(index) {


                    steps.removeClass("active");
                    $(steps[index]).addClass("active");
                    $(".step-item").removeClass("active completed");
                    $(".step-item").each(function(i) {
                        if (i < index) $(this).addClass("completed");
                        else if (i === index) $(this).addClass("active");
                    });

                }

                function saveStep(step, formData, onSuccess) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var editForm = $("#application-edit-form");
                    var url = editForm.attr('action');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            // Clear all errors
                            $(".form-control").removeClass("is-invalid");
                            $(".invalid-feedback").remove();
                            $("#errors").html("");
                            onSuccess();
                            console.log(step);

                            var totalSteps = steps.length;
                            var finalStep = step;

                            if (totalSteps == finalStep) {
                                $('#main-application').hide();
                                $('.success-wrapper').css({
                                    "display": "flex"
                                });
                            }

                        },
                        error: function(xhr) {
                            console.log(xhr);
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;

                                // Clear old errors
                                $(".form-control").removeClass("is-invalid");
                                $(".invalid-feedback").remove();

                                $.each(errors, function(key, messages) {



                                    let input;

                                    if (key.startsWith("fields.")) {
                                        // Convert dot notation to bracket name
                                        let fieldName = key.replace("fields.", ""); // center_no
                                        let inputName = `fields[${fieldName}]`; // fields[center_no]
                                        // Escape brackets for jQuery selector
                                        let inputSelector = inputName.replace(/([[\]])/g, "\\$1");
                                        input = $(`[name="${inputSelector}"]`);
                                    } else {
                                        // Normal field (not part of dynamic fields array)
                                        let inputSelector = key.replace(/([[\]])/g, "\\$1");
                                        input = $(`[name="${inputSelector}"]`);
                                    }
                                    // let input = $(`[name="${key}"]`);
                                    if (input.length) {
                                        input.addClass("is-invalid");
                                        // insert after field
                                        input.after(
                                            `<div class="invalid-feedback">${messages[0]}</div>`
                                        );
                                    }
                                });

                                // Scroll to first invalid input
                                let firstError = $(".is-invalid:first");
                                if (firstError.length) {
                                    $("html, body").animate({
                                        scrollTop: firstError.offset().top - 100
                                    }, 300);
                                }
                            }
                        }
                    });
                }


                // Make signature-area clickable
                $("#signatureArea").on("click", function(e) {
                    // Prevent double trigger if user clicks directly on input
                    if (!$(e.target).is("#signatureUpload")) {
                        $("#signatureUpload").trigger("click");
                    }
                });


                // Handle preview for image/pdf
                $("#signatureUpload").on("change", function() {
                    let file = this.files[0];
                    if (file) {
                        let fileType = file.type;

                        // Image (JPEG)
                        if (fileType === "image/jpeg" || fileType === "image/jpg" || fileType === "image/png") {
                            let reader = new FileReader();
                            reader.onload = function(e) {
                                $(".signature-preview").html(
                                    `<img src="${e.target.result}" alt="Signature">`
                                );
                                $(".signature-placeholder").hide();
                            };
                            reader.readAsDataURL(file);
                        }

                        // PDF
                        else if (fileType === "application/pdf") {
                            $(".signature-preview").html(
                                `<p><i class="fas fa-file-pdf text-danger"></i> Uploaded: <strong>${file.name}</strong></p>`
                            );
                            $(".signature-placeholder").hide();
                        }

                        // Unsupported file
                        else {
                            alert("Only JPEG and PDF files are allowed.");
                            $(this).val(""); // reset input
                        }
                    }
                });

                // Enable/Disable submit button
                $('#terms').change(function() {
                    $('#submit-application').attr('disabled', !this.checked);
                });



                function completedContract() {
                    var completed = $('#status').val();

                    if (completed == 'complete') {
                        $('#main-application').hide();
                        $('.success-wrapper').css({
                            "display": "flex"
                        });

                    }
                }
                // Payment method change
                $('#paymentmethods').change(function() {
                    var id = $(this).val();
                    if (!id) return;

                    $.ajax({
                        url: '{{ $paymentUrl }}',
                        type: 'GET',
                        data: {
                            payment_method_id: id
                        },
                        success: function(data) {
                            $("#paymentmethods-attributes").html(data.payment_methods);
                            if (window.intlTelInput) initIntlTelInput();
                        },
                        error: function(err) {
                            console.error(err);
                        }
                    });
                });



                function initIntlTelInput() {
                    var phone = document.querySelector("#payable_phone_number");
                    if (!phone) return;

                    window.iti = window.intlTelInput(phone, {
                        initialCountry: "LS",
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/utils.js",
                        geoIpLookup: function(callback) {
                            var val = phone.value;
                            phone.value = '';
                            $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "LS";
                                callback(countryCode);
                                setTimeout(() => {
                                    phone.value = val;
                                }, 10);
                            });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
