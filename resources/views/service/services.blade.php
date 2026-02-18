<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>eService | Examinations Council of Lesotho</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel='stylesheet'
        href='https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css'>
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/all.css') }}">
    <link rel="stylesheet"
        href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
    <script src='https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/intlTelInput.js'></script>

    <!-- custom styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/css/intlTelInput.css'>




    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.min.js"></script>
    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>
    <script>
        liteboxInitialise('https://portal.nedsecure.co.za', "eserviceform");
    </script>

</head>

<body>

    <body>
        <!-- ============================================================== -->
        <!-- Preloader -->
        <!-- ============================================================== -->
        {{-- <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div> --}}



        <main>
            <!-- logo section -->
            <section id="logo_container">
                <div class="logo_box">
                    <img src="assets/images/logo.png" alt="" class="logo">
                </div>
                <div class="info-online">
                    <h2><strong>Examinations Council of Lesotho</strong></h2>
                  <p>eServices</p>
                </div>
            </section>
            <!-- logo section -->
            <div class="container-flud" id="eservices">
                <form class="row" id="eserviceform" method="POST" action="{{ route('services.multiform') }}"
                    enctype="multipart/form-data">


                    @csrf
                    <div class="col-sm-12  col-md-8 col-lg-8">
                        <!-- Step Navigation -->
                        <div class="d-flex align-items-start  sm:mb-5 progress-form__tabs" role="tablist">
                            <button id="progress-form__tab-1" class="flex-1 px-0 pt-2 progress-form__tabs-item"
                                type="button" role="eservice-tab" aria-controls="progress-form__panel-1"
                                aria-selected="true">
                                <span class="d-block step" aria-hidden="true">1</span>
                                Personal info
                            </button>
                            <button id="progress-form__tab-2" class="flex-1 px-0 pt-2 progress-form__tabs-item"
                                type="button" role="eservice-tab" aria-controls="progress-form__panel-2"
                                aria-selected="false" tabindex="-1" aria-disabled="true">
                                <span class="d-block step" aria-hidden="true">2</span>
                                Requirements
                            </button>
                            <button id="progress-form__tab-3" class="flex-1 px-0 pt-2 progress-form__tabs-item"
                                type="button" role="eservice-tab" aria-controls="progress-form__panel-3"
                                aria-selected="false" tabindex="-1" aria-disabled="true">
                                <span class="d-block step" aria-hidden="true">3</span>
                                Payments
                            </button>
                        </div>
                        <!-- / End Step Navigation -->
                        <!-- Step 1 -->


                        <section id="progress-form__panel-1" role="eservice-tabpanel"
                            aria-labelledby="progress-form__tab-1" tabindex="0">
                            <div class="mt-3 sm:mt-0 form__field">
                                <label for="select-service">
                                    Services
                                    <span data-required="true" aria-hidden="true"></span>
                                </label>
                                <input type="hidden" name="total_sale_price" value="">
                                <input type="hidden" name="sigle_sale_price" value="">
                                <select id="select-service" name="service" autocomplete="shipping address-level1"
                                    required>
                                    <option value="" disabled selected>Please select</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->desciption }}</option>
                                    @endforeach
                                    <option value="status">CHECK STATUS</option>
                                </select>
                            </div>

                            <fieldset class="mt-3 form__field">
                                <div class="form__radios" id="service-items">
                                </div>
                            </fieldset>
                            <div id="personal-info">

                            </div>
                        </section>
                        <!-- / End Step 1 -->
                        <!-- Step 2 -->
                        <section id="progress-form__panel-2" role="eservice-tabpanel"
                            aria-labelledby="progress-form__tab-2" tabindex="0" hidden>
                        </section>
                        <!-- / End Step 2 -->
                        <!-- Step 3 -->
                        <section id="progress-form__panel-3" role="eservice-tabpanel"
                            aria-labelledby="progress-form__tab-3" tabindex="0" hidden>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            </div>
                            <div class="nav nav-tabs payment-method" id="nav-tab" role="tablist">
                            </div>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade p-3" id="credit-card" role="tabpanel"
                                    aria-labelledby="credit-card-tab">
                                    <h2>EFT (Card Transactions)</h2>
                                    <div class="mt-2 sm:mt-0 form__field">
                                        <a id="iveri-litebox-button">Pay now</a>
                                    </div>
                                    <div class="form-group">

                                        <!-- Iveri start-->
                                        <input type="hidden" value="Mr." readonly="readonly"
                                            name="Ecom_BillTo_Postal_Name_Prefix" id="Ecom_BillTo_Postal_Name_Prefix"
                                            class="clsInputReadOnlyText" />
                                        <input name="Ecom_BillTo_Postal_Name_First" readonly="readonly"
                                            type="hidden" value="John" id="Ecom_BillTo_Postal_Name_First"
                                            class="clsInputReadOnlyText" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_Name_Middle"
                                            id="Ecom_BillTo_Postal_Name_Middle" />

                                        <input name="Ecom_BillTo_Postal_Name_Last" readonly="readonly" type="hidden"
                                            value="Doe" style="width: 20px;" id="Ecom_BillTo_Postal_Name_Last"
                                            class="clsInputReadOnlyText" />
                                        <input name="Ecom_BillTo_Online_Email" readonly="readonly" type="hidden"
                                            value="jdoe@mail.com" maxlength="50" id="Ecom_BillTo_Online_Email"
                                            class="clsInputReadOnlyText" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Postal_Street_Line1"
                                            id="Ecom_ShipTo_Postal_Street_Line1" value="50 Sunny Drive Avenue" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Postal_Street_Line2"
                                            id="Ecom_ShipTo_Postal_Street_Line2" value="Sunsetville" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Postal_City" id="Ecom_ShipTo_Postal_City"
                                            value="Johannesburg" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Postal_StateProv" id="Ecom_ShipTo_Postal_StateProv"
                                            value="Gauteng" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Postal_PostalCode" id="Ecom_ShipTo_Postal_PostalCode"
                                            value="2185" />

                                        <input name="Ecom_ConsumerOrderID" readonly="readonly" type="hidden"
                                            value="AUTOGENERATE" maxlength="20" id="Ecom_ConsumerOrderID"
                                            class="clsInputReadOnlyText" />
                                        <input type="hidden" name="Ecom_SchemaVersion" id="Ecom_SchemaVersion" />
                                        <input type="hidden" name="Ecom_TransactionComplete"
                                            id="Ecom_TransactionComplete" value="false" />
                                        <input type="hidden" name="Lite_Authorisation" id="Lite_Authorisation"
                                            value="false" />
                                        <input type="hidden" name="Lite_Version" id="Lite_Version"
                                            value="2.0" />
                                        <!-- Ecml end-->

                                        <!-- Lite_Order_LineItems_Product -->
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Lite_Order_LineItems_Product_1" id="Lite_Order_LineItems_Product_1"
                                            value="Total Subjects" />
                                        <input type="hidden" readonly="readonly"
                                            name="Lite_Order_LineItems_Quantity_1"
                                            id="Lite_Order_LineItems_Quantity_1" value="1" />

                                        <!-- Lite_Order_LineItems_Amount -->
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Transaction_LineItems_Amount_1" id="Transaction_LineItems_Amount_1"
                                            value="100" />
                                        <input type="hidden" name="Lite_Order_LineItems_Amount_1"
                                            id="Lite_Order_LineItems_Amount_1" value="100" />


                                        <!-- Transaction Amount -->
                                        <input name="Transaction_Amount" id="Transaction_Amount" type="hidden"
                                            value="100" class="clsInputText" value="100" />
                                        <input type="hidden" value="100" name="Lite_Order_Amount"
                                            id="Lite_Order_Amount" />

                                        <!-- Merchant_Application ID -->
                                        <input name="Merchant_ApplicationID" type="hidden"
                                            value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}" maxlength="40"
                                            id="Merchant_ApplicationID" class="clsInputText" />
                                        <input type="hidden" name="Lite_Merchant_ApplicationID"
                                            value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                            id="Lite_Merchant_ApplicationID" />
                                        <input type="hidden" name="Ecom_Payment_Card_Protocols"
                                            id="Ecom_Payment_Card_Protocols" value="iVeri" />
                                        <!-- Other Optional fields that can be set -->
                                        <input type="hidden" name="Lite_Order_Terminal" id="Lite_Order_Terminal"
                                            value="77777001" />
                                        <input type="hidden" name="Lite_Order_AuthorisationCode"
                                            id="Lite_Order_AuthorisationCode" />
                                        <input type="hidden" name="Lite_Website_TextColor"
                                            id="Lite_Website_TextColor" value="#ffffff" />
                                        <input type="hidden" name="Lite_Website_BGColor" id="Lite_Website_BGColor"
                                            value="#fff" />
                                        <input type="hidden" name="Lite_ConsumerOrderID_PreFix"
                                            id="Lite_ConsumerOrderID_PreFix" value="LITE" />
                                        <input type="hidden" name="Lite_Website_Successful_Url"
                                            id="Lite_Website_Successful_Url"
                                            value="https://examples.iveri.net/Lite/Result.asp" />
                                        <input type="hidden" name="Lite_Website_Fail_Url" id="Lite_Website_Fail_Url"
                                            value="https://examples.iveri.net/Lite/Result.asp" />
                                        <input type="hidden" name="Lite_Website_Error_Url"
                                            id="Lite_Website_Error_Url"
                                            value="https://examples.iveri.net/Lite/Result.asp" />
                                        <input type="hidden" name="Lite_Website_Trylater_Url"
                                            id="Lite_Website_Trylater_Url"
                                            value="https://examples.iveri.net/Lite/Result.asp" />


                                        <!-- Ecml start-->

                                        <!-- ShipTo Additional tags -->
                                        <input type="hidden" name="Ecom_ShipTo_Postal_Name_Prefix"
                                            id="Ecom_ShipTo_Postal_Name_Prefix" />
                                        <input type="hidden" name="Ecom_ShipTo_Postal_Name_First"
                                            id="Ecom_ShipTo_Postal_Name_First" />
                                        <input type="hidden" name="Ecom_ShipTo_Postal_Name_Middle"
                                            id="Ecom_ShipTo_Postal_Name_Middle" />
                                        <input type="hidden" name="Ecom_ShipTo_Postal_Name_Last"
                                            id="Ecom_ShipTo_Postal_Name_Last" />
                                        <input type="hidden" name="Ecom_ShipTo_Postal_Name_Suffix"
                                            id="Ecom_ShipTo_Postal_Name_Suffix" />

                                        <input type="hidden" name="Ecom_ShipTo_Postal_Street_Line3"
                                            id="Ecom_ShipTo_Postal_Street_Line3" />
                                        <input type="hidden" name="Ecom_ShipTo_Postal_CountryCode"
                                            id="Ecom_ShipTo_Postal_CountryCode" />
                                        <input type="hidden" readonly="readonly" class="clsInputReadOnlyText"
                                            name="Ecom_ShipTo_Telecom_Phone_Number"
                                            id="Ecom_ShipTo_Telecom_Phone_Number" />

                                        <input type="hidden" name="Ecom_ShipTo_Online_Email"
                                            id="Ecom_ShipTo_Online_Email" />

                                        <!-- ReceiptTo -->
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Name_Prefix"
                                            id="Ecom_ReceiptTo_Postal_Name_Prefix" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Name_First"
                                            id="Ecom_ReceiptTo_Postal_Name_First" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Name_Middle"
                                            id="Ecom_ReceiptTo_Postal_Name_Middle" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Name_Last"
                                            id="Ecom_ReceiptTo_Postal_Name_Last" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Name_Suffix"
                                            id="Ecom_ReceiptTo_Postal_Name_Suffix" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Street_Line1"
                                            id="Ecom_ReceiptTo_Postal_Street_Line1" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Street_Line2"
                                            id="Ecom_ReceiptTo_Postal_Street_Line2" />

                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_Street_Line3"
                                            id="Ecom_ReceiptTo_Postal_Street_Line3" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_City"
                                            id="Ecom_ReceiptTo_Postal_City" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_StateProv"
                                            id="Ecom_ReceiptTo_Postal_StateProv" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_PostalCode"
                                            id="Ecom_ReceiptTo_Postal_PostalCode" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Postal_CountryCode"
                                            id="Ecom_ReceiptTo_Postal_CountryCode" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Telecom_Phone_Number"
                                            id="Ecom_ReceiptTo_Telecom_Phone_Number" />
                                        <input type="hidden" name="Ecom_ReceiptTo_Online_Email"
                                            id="Ecom_ReceiptTo_Online_Email" />

                                        <!-- BillTo -->
                                        <input type="hidden" name="Ecom_BillTo_Postal_Name_Suffix"
                                            id="Ecom_BillTo_Postal_Name_Suffix" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_Street_Line1"
                                            id="Ecom_BillTo_Postal_Street_Line1" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_Street_Line2"
                                            id="Ecom_BillTo_Postal_Street_Line2" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_Street_Line3"
                                            id="Ecom_BillTo_Postal_Street_Line3" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_City"
                                            id="Ecom_BillTo_Postal_City" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_StateProv"
                                            id="Ecom_BillTo_Postal_StateProv" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_PostalCode"
                                            id="Ecom_BillTo_Postal_PostalCode" />
                                        <input type="hidden" name="Ecom_BillTo_Postal_CountryCode"
                                            id="Ecom_BillTo_Postal_CountryCode" />
                                        <input type="hidden" name="Ecom_BillTo_Telecom_Phone_Number"
                                            id="Ecom_BillTo_Telecom_Phone_Number" />
                                    </div>
                                </div>
                                <div class="tab-pane fade p-3 " id="eco-cash" role="tabpanel"
                                    aria-labelledby="eco-cash-tab">
                                    <h2>EcoCash</h2>
                                    <div class="form-group">
                                        <label for="ecocash_mobile">Phone number</label>
                                        <input type="text" name="ecocash_mobile" class="form-control"
                                            id="ecocash_mobile" placeholder="*Phone number">
                                    </div>
                                    <a href="javascript:void(0)" id="ecocash_pay" class="btn btn-primary">Pay now
                                    </a>
                                </div>
                                <div class="tab-pane fade p-3" id="vcl-mpesa" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <h2>M-PESA</h2>
                                    <div class="form-group">
                                        <label for="mpesa_mobile">Phone number</label>
                                        <input type="text" name="mpesa_mobile" class="form-control"
                                            id="mpesa_mobile" placeholder="*Phone number">
                                    </div>
                                    <a href="javascript:void(0)" id="mpesa_pay" class="btn btn-primary">Pay now
                                    </a>
                                </div>
                                <div class="tab-pane fade p-3" id="nav-contact" role="tabpanel"
                                    aria-labelledby="nav-contact-tab">
                                    <h2>Cash Deposit</h2>
                                    <button type="button" class="btn btn-primary modalTrigger" data-toggle="modal"
                                        data-target="#confirmationModal">
                                        Upload proof of payment
                                    </button>
                                </div>
                            </div>
                            <div
                                class="d-flex sm:flex-row align-items-center justify-center sm:justify-end mt-4 sm:mt-5">
                                <button type="button" class="mt-1 sm:mt-0 button--simple" data-action="prev">
                                    Back
                                </button>
                                <button id="btn-submit" disabled type="submit">
                                    Submit
                                </button>
                            </div>
                        </section>
                        <!-- / End Step 3 -->
                        <!-- Thank You -->
                        <section id="progress-form__thank-you" hidden>
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="col-md-12">
                                    <div class="border border-3 border-success"></div>
                                    <div class="card  bg-white shadow p-5">
                                        <div class="mb-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-success"
                                                width="75" height="75" fill="currentColor"
                                                class="bi bi-check-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path
                                                    d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <h1>Thank You !</h1>
                                            <p> Your Reference # is: <span class="reference_number"></span>
                                            </p>
                                            <p>You will receive an order confirmation email with details of your
                                                order
                                                and a link to track your process.</p>
                                             <a href="/services" class="btn btn-outline-success" target="_blank">Back Home</a>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
                        <!-- / End Thank You -->
                    </div>
                    <div class="col-sm-12 col-md-4  col-lg-4" id="service-order">
                        <table id="service-table">
                            <tr>
                                <th colspan="2">Your Invoice</th>

                            </tr>
                        </table>
                    </div>



                </form>
            </div>
        </main>




        <!-- Modal HTML iveri -->
        <div id="iveri-litebox" class="center-block"></div>
        <!-- Modal HTML confirrmation -->

        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/js/services.js') }}"></script>
        <script>
            /**** Lite Box*******/
            function liteboxComplete(data) {
                var formData = new FormData($("#service-items").parents('form')[0]);
                $.each(($.parseJSON(data)), function(key, value) {
                    formData.append(key, value);
                });
                formData.append("payment", "CreditCard");
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                $.ajax({
                    url: "{{ route('services.transaction') }}",
                    type: 'POST',
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        return myXhr;
                    },
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(response) {
                    if ($.isEmptyObject(response.errors)) {
                        $(".modal-backdrop").hide();
                        handleSuccess(response);
                    } else {
                        $(".modal-backdrop").hide();
                        swal({
                            title: "Error!",
                            text: response.errors['bank_card'][0],
                            icon: "error",
                        });
                    }
                }).fail(function(xhr, status, error) {
                    console.log(error)
                });

            }
            /****End Lite Box*******/
            $(document).ready(function() {
                /**** initailize Select2*******/
                initailizeSelect2();
                $(document).on("change", "#select-service", function() {
                    var service_id = $(this).val();
                    if (service_id) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('services.serviceItem') }}",
                            data: {
                                service: service_id
                            },
                            success: function(response) {
                                $("#service-items").html(response.html);

                            }
                        });
                    } else {
                        $("#service-items").empty();
                    }
                });
                $(document).on("click", "[name='serviceItem']", function() {
                    addToCart(this);
                    var service_id = $(this).data('id');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('services.serviceRequirements') }}",
                        data: {
                            service: service_id
                        },
                        success: function(response) {
                            $("#personal-info").html(response.personalInfoHTML);
                            $("#progress-form__panel-2").html(response.attributesHTML);
                            $(".payment-method").html(response.paymentsHTML);
                            initailizeSelect2();
                            initailizeIntlTelInput();



                        }
                    });


                });

                $(document).on("click", "#check-status", function(ev) {
                    var reference_no = $("[name='reference_no']").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('services.checkstatus') }}",
                        data: {
                            reference_no: reference_no
                        },
                        success: function(response) {

                            $("#personal-info").html("");
                            $("#progress-form__panel-2").html("");
                            $(".payment-method").html("");
                            if ($.isEmptyObject(response.errors)) {
                                $(".statuses-container").html(response.status);
                                $(".status-progress-wrap").css({
                                    "display": "flex",
                                });
                                $(".invalid-feedback").remove();
                            } else {
                                $("#personal-info").html("");
                                $("#progress-form__panel-2").html("");
                                $(".payment-method").html("");
                                $(".statuses-container").html("");
                                printErrorMsg("#eserviceform", response.errors);
                            }
                        }
                    });


                });


                $(document).on('input', 'input', function() {
                    formUpdate();

                });



                /**** MPESA *******/
                $(document).on("click", "#mpesa_pay", function() {
                    vclMpesaComplete($(this));
                });

                function vclMpesaComplete(element) {
                    var formData = new FormData($("#service-items").parents('form')[0]);
                    formData.append("payment", "VclMpesa");
                    //   M-Pesa request
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });
                    var caption = element.html();
                    $.ajax({
                        url: "{{ route('services.transaction') }}",
                        type: 'POST',
                        xhr: function() {
                            var myXhr = $.ajaxSettings.xhr();
                            return myXhr;
                        },
                        data: formData,
                        beforeSend: function() {
                            element.prop('disabled', true).html("Processing...");
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    }).done(function(response) {
                        if ($.isEmptyObject(response.errors)) {
                            handleSuccess(response);
                            element.prop('disabled', false).html(caption);
                        } else {
                            printErrorMsg("#eserviceform", response.errors);
                            element.prop('disabled', false).html(caption);
                        }
                    }).fail(function(xhr, status, error) {
                        element.prop('disabled', false).html(caption);
                    });
                }
                /****End MPESA *******/


                /**** EcoCash *******/
                $(document).on("click", "#ecocash_pay", function() {
                    ecoCashComplete($(this));
                });
                function ecoCashComplete(element) {
                    var formData = new FormData($("#service-items").parents('form')[0]);
                    formData.append("payment", "EcoCash");
                    //   EcoCash request
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });
                    var caption = element.html();
                    $.ajax({
                        url: "{{ route('services.transaction') }}",
                        type: 'POST',
                        xhr: function() {
                            var myXhr = $.ajaxSettings.xhr();
                            return myXhr;
                        },
                        data: formData,
                        beforeSend: function() {
                            element.prop('disabled', true).html("Processing...");
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    }).done(function(response) {
                        if ($.isEmptyObject(response.errors)) {
                            handleSuccess(response);
                            element.prop('disabled', false).html(caption);
                        } else {
                            printErrorMsg("#eserviceform", response.errors);
                            element.prop('disabled', false).html(caption);
                        }
                    }).fail(function(xhr, status, error) {
                        element.prop('disabled', false).html(caption);
                    });
                }
                /****END EcoCash *******/

                /****Update Form *******/

                function formUpdate() {

                    var first_name = $("[name='first_name']").val();
                    var last_name = $("[name='last_name']").val();
                    var email = $("[name='email']").val();
                    var sales_item = $("[name='serviceItem']").val();
                    var total_price = $("[name='total_sale_price']").val();

                    total_price = total_price * 100;
                    $("#Ecom_BillTo_Postal_Name_First").val(first_name);
                    $("#Ecom_BillTo_Postal_Name_Last").val(last_name);
                    $("#Ecom_BillTo_Postal_Name_Prefix").val("Miss. or Mrs.  or Mr");
                    $("#Ecom_BillTo_Online_Email").val(email);
                    $("#Transaction_Amount").val(total_price);
                    $("#Transaction_LineItems_Amount_1").val(total_price);
                    $("#Lite_Order_LineItems_Amount_1").val(total_price);
                    $("#Lite_Order_Amount").val(total_price);
                    $("#Lite_Order_LineItems_Amount_1").val(total_price);
                    $("#Lite_Order_LineItems_Quantity_1").val(1);
                    $("#Lite_Order_LineItems_Product_1").val(`${sales_item}`);
                }
                /****End Update Form *******/
                function handleSuccess(response) {
                    $("#eserviceform [role=eservice-tabpanel]").remove();
                    $("#eserviceform  .progress-form__tabs").remove();
                    // Clear all HTML Nodes that are not the thank you panel
                    $("#progress-form__thank-you").removeAttr('hidden');
                    $("#btn-submit").prop('disable');
                    $("#btn-submit").trigger("click");
                    $(".reference_number").html(response.reference_number);

                }
                /**** ADD TO CART functions *******/
                function addToCart(elem) {
                    //init
                    var price = $(elem).data('price');
                    var serviceName = $(elem).data('service');
                    var carttable = ""
                    //create product object
                    var services = {
                        price: price,
                        serviceName: serviceName
                    };
                    carttable = `<tr><th colspan='2'>Your Invoice</th></tr>
                       <tr><td>${services.serviceName}</td><td>M${services.price.toFixed(2)}</td></tr>
                        <tr><td>Total</td><td class="single-price">M${services.price.toFixed(2)}</td></tr>`;
                    $("#service-table").html(carttable);
                    $("[name='total_sale_price']").val(services.price);
                    $("[name='sigle_sale_price']").val(services.price);





                }

                /********** Auto Search For School Centres **************/
                /**** Initailize Select2 functions *******/
                function initailizeSelect2() {

                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $(".livesearch-all-centers").select2({
                        placeholder: "Select the Center",
                        ajax: {
                            url: "{{ route('services.autocomplete') }}",
                            method: "POST",
                            dataType: "json",
                            delay: 250,
                            processResults: function(data) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            text: `${item.center_name} (${item.level})`,
                                            id: item.center_no,
                                        };
                                    }),
                                };
                            },
                            cache: true,
                        },
                    })
                }

                $(document).on("change", ".livesearch-all-centers", function() {
                    var centre_no = $(this).val();
                    var level = $("#level").find("option:selected").data("level");
                    var session = 4;
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: "/registeration-center-subjects",
                        method: "POST",
                        data: {
                            centre_no: centre_no,
                            level: level,
                            session: session
                        },
                        success: function(data) {
                            $(".subjects_selection").html(data.subjectsHTML);
                            $('.subjects_selection  input[type="checkbox"]').prop('required', true);

                        },
                    });
                });
                /**** Initailize Select2 functions *******/


                $(document).on("keyup", "#candidate_no", function() {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: "/services/valid-candidate",
                        method: "POST",
                        data: {
                            candidate_no: $(this).val()
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.errors)) {
                                $("#is_candidate").val(data.candidate_no);
                            } else {
                                printErrorMsg('#eservicesForm', data.errors);
                            }

                        },
                    });
                });


                /********** Select subjects ****************/
                $(document).on("change", ".subject", function() {
                    checkedCheckBox();
                });
                $(document).on("click", ".subjects_selection  input", function() {
                    if ($(this).prop("checked")) {
                        var input_classes = $(this).attr("class").split(" ");
                        var className;
                        $.each(input_classes, function() {
                            if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
                        });
                        $("." + className).prop("checked", false);
                        $(this).prop("checked", true);
                    } else {
                        var input_classes = $(this).attr("class").split(" ");
                        var className;
                        $.each(input_classes, function() {
                            if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
                        });
                        $("." + className).prop("checked", false);
                        $(this).prop("checked", false);
                    }
                    checkedCheckBox();
                });

                /********** Checked Subjects **************/
                function checkedCheckBox() {
                    var numberOfChecked = $(
                        ".subjects_selection input:checkbox:checked"
                    ).length;
                    var single_price = $("[name='sigle_sale_price']").val();
                    var subjects_total = (numberOfChecked * parseInt(single_price));
                    if (numberOfChecked <= 0) {
                        $("#is_subject").val('');
                        $(".single-price").html(`M${subjects_total.toFixed(2)}`);
                    } else {
                        $("#is_subject").val(numberOfChecked);
                        $("[name='total_sale_price']").val(subjects_total);
                        $(".single-price").html(`M${subjects_total.toFixed(2)}`);
                    }
                }
                /********** Checked Subjects End**************/


                function initailizeIntlTelInput() {
                    var phone = document.querySelector("#phone");
                    var iti = window.intlTelInput(phone, {
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/utils.js",
                        initialCountry: "auto",
                        geoIpLookup: function(callback) {
                            var elt = document.getElementById('phone'),
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
                /****  Print errors End*******/

                function isJsonString(str) {
                    try {
                        JSON.parse(str);
                    } catch (e) {
                        return false;
                    }
                    return true;
                }
            });
        </script>
        <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('assets/js/additional-methods.js') }}"></script>
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
        @stack('scripts')
        <script src="{{ asset('assets/js/main.js') }}"></script>

    </body>

</html>