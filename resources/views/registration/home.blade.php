<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Examinations Online Registration </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel='stylesheet'
        href='https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css'>
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/all.css') }}">
    <link rel="stylesheet"
        href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">

    <!-- custom styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />


    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">

    <link href="{{ asset('school/assets/css/toastr.min.css') }}" rel="stylesheet" />

    <script src='https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js'></script>

    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.min.js"></script>
    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>
    <script>
        liteboxInitialise('https://portal.nedsecure.co.za', "msform");
    </script>

</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>


    <main>
        <!-- logo section -->
        <section id="logo_container">
            <div class="progressbar">
                <ul id="progressbar_content">
                    <li class="active" id="personal">Personal Information & Verification </li>
                    <li id="nextKin">Next OF Kin</li>
                    <li id="exam_group">Exam Session</li>
                    <li id="subjects_selection">Subjects Selection</li>
                    <li id="invoice">Bill</li>
                    <li id="payment">Payment</li>
                    <li id="confirm">Finish</li>
                </ul>
            </div>
        </section>
        <!-- logo section -->

        <!-- MultiStep Form -->
        <section id="grad1">
            <div class="container">
                <div class="row justify-content-center mt-0">

                    <div class="col-md-11 text-center p-0 mt-3 mb-2">
                        <div class="card">
                            <!-- progressbar -->
                            <!-- <p>Fill all form field to go to next step</p> -->
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <form action="" method="post" id="msform">
                                        @csrf
                                        <input type="hidden" name="fee_group_id" id="fee_group_id">
                                        <input type="hidden" name="fine" id="fine">
                                        <!-- fieldsets -->

                                        <!-- Personal Information & Verification -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container">
                                                <div class="form-card search-box">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2">Personal Information </h2>
                                                    </div>
                                                    <div class="form-group">
                                                        <h2 class="fs-title">Registration Alternative</h2>
                                                        <div class="registration_alternatives">
                                                            <label class="alternative basic-alternative"
                                                                for="existing-candidate-number">
                                                                <input type="radio" name="alternative"
                                                                    id="existing-candidate-number"
                                                                    value="existing-candidate" checked />
                                                                <div class="alternative-content">
                                                                    <i class="fas fa-user-edit" loading="lazy"></i>
                                                                    <div class="alternative-details">
                                                                        <span>Existing Candidate Number</span>
                                                                        <p>2006-{{ date('Y') - 1 }}</p>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="alternative complete-alternative"
                                                                for="new-candidate-no">
                                                                <input type="radio" id="new-candidate-no"
                                                                    name="alternative" value="new-candidate" />
                                                                <div class="alternative-content">
                                                                    <i class="fas fa-plus-square" loading="lazy"></i>
                                                                    <div class="alternative-details">
                                                                        <span>New Candidate Number</span>
                                                                        <p>{{ date('Y') }}</p>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div id="alternative-tab-content">
                                                        <div class="alternative-tab-pane" id="existing-candidate">

                                                        </div>
                                                        <div class="alternative-tab-pane" id="new-candidate">

                                                        </div>
                                                    </div>

                                                </div>
                                                <input type="button" name="personlinfo"
                                                    class="next action-button personlinfo" value="Next">
                                            </div>
                                        </fieldset>
                                        <!-- Next of Kin  -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container">
                                                <div class="form-card">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2">Next of Kin </h2>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-12">
                                                            <label for="guardian_type">Relationship</label>
                                                            <select name="guardian_type"
                                                                class="form-control required " id="guardian_type">
                                                                <option value="">Please select
                                                                    relationship</option>
                                                                @foreach ($guardian_types as $guardian_type)
                                                                    <option value="{{ $guardian_type->id }}">
                                                                        {{ $guardian_type->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                        <div class="form-group col-12">
                                                            <label for="guardian_national_id">National ID</label>
                                                            <input type="text" class="form-control "
                                                                id="guardian_national_id" name="guardian_national_id"
                                                                placeholder="National Id" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_name">Other
                                                                name(s)</label>
                                                            <input type="text" class="form-control required  "
                                                                id="guardian_name" name="guardian_name"
                                                                placeholder="Name" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_surname">Surname</label>
                                                            <input type="text" class="form-control required  "
                                                                id="guardian_surname" name="guardian_surname"
                                                                placeholder="Surname" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_email">Email</label>
                                                            <input type="text" class="form-control required"
                                                                id="guardian_email" name="guardian_email"
                                                                placeholder="Email" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_phone">Phone
                                                                number</label>
                                                            <input type="text" class="form-control required  "
                                                                id="guardian_phone_number"
                                                                name="guardian_phone_number"
                                                                placeholder="Phone number" value="">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-6">
                                                            <label for="guardian_postal_address">Postal
                                                                address </label>
                                                            <input type="text" class="form-control required "
                                                                id="guardian_postal_address"
                                                                name="guardian_postal_address"
                                                                placeholder="P.O.Box 507" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_physical_address">Physical
                                                                address</label>
                                                            <input type="text" class="form-control required  "
                                                                id="guardian_physical_address"
                                                                name="guardian_physical_address"
                                                                placeholder="Selakhapane" value="">
                                                        </div>

                                                        <div class="form-group col-6">
                                                            <label for="guardian_village">Village</label>
                                                            <input type="text" class="form-control required "
                                                                id="guardian_village" name="guardian_village"
                                                                placeholder="Khubetsoana" value="">
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label for="guardian_district">District</label>
                                                            <select class="form-control required  "
                                                                name="guardian_district" id="guardian_district">
                                                                <option value="">Select
                                                                    district</option>
                                                                @foreach ($districts as $district)
                                                                    <option value="{{ $district->district }}">
                                                                        {{ $district->district }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="button" name="previous"
                                                    class="previous action-button-previous" value="Previous">
                                                <input type="submit" name="guardian" class="next action-button"
                                                    value="Next">

                                            </div>
                                        </fieldset>
                                        <!-- Level -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container">
                                                <div class="form-card">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2"> Examination Level & Session</h2>
                                                    </div>
                                                    <div class="form-group">
                                                        <h2 class="fs-title">Level</h2>

                                                        <select name="level" class=" form-control required "
                                                            id="level">
                                                            <option value="">Please select Level</option>
                                                            @foreach ($levels as $level)
                                                                <option value="{{ $level->level }}"
                                                                    data-level="{{ $level->id }}">
                                                                    {{ $level->level }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <h2 class="fs-title">Session</h2>
                                                        <select class="form-control required" name="session"
                                                            id="session">
                                                            <option value="">Please select Session</option>
                                                            @foreach ($sessions as $session)
                                                                <option value="{{ $session->session }}"
                                                                    data-session="{{ $session->id }}">
                                                                    {{ $session->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="button" name="previous"
                                                    class="previous action-button-previous" value="Previous">
                                                <input type="button" name="exam-group" class="next action-button"
                                                    value="Next">
                                            </div>
                                        </fieldset>
                                        <!--Subjects Selection  -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container">
                                                <div class="form-card">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2">Centres Selection LGCSE</h2>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 school-centre">
                                                            <h2 class="fs-title">* School Centre</h2>
                                                            <select class="livesearch-centers form-control required "
                                                                name="center" id="center"></select>
                                                        </div>
                                                        <input type="hidden" name="selected-center"
                                                            id="selected-center" value="">

                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <h2 class="fs-title">* Subjects Selection</h2>
                                                    </div>
                                                    <div class="row subjects_selection">

                                                    </div>
                                                    <div class="total-price">
                                                        <table>
                                                            <tr>
                                                                <td>Number of Subjects</td>
                                                                <td class="subject_number">0</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Total</td>
                                                                <td class="total">0</td>
                                                            </tr>
                                                            <tr>
                                                                <td id="total_amount"><input type="hidden"
                                                                        name="total_amount" class="required"
                                                                        value="">
                                                                    <input type="hidden" class="required "
                                                                        name="number_of_subjects"
                                                                        id="number_of_subjects" value="">
                                                                </td>
                                                            </tr>

                                                        </table>
                                                    </div>
                                                    <div class="col-md-12 disclaimer_checkbox">
                                                        <label>
                                                            <input type="checkbox" name="disclaimer" value="">
                                                            I agree
                                                            to the
                                                        </label>
                                                        <a href="{{ asset('TERMS.pdf') }}" target="_blank"> terms &
                                                            conditions and privacy
                                                            policy</a>
                                                    </div>
                                                </div>

                                                <input type="button" name="previous-Subjects"
                                                    class="previous action-button-previous" value="Previous">
                                                <input type="submit" name="next-billing" class="next action-button"
                                                    id="billing" value="Next">
                                            </div>
                                        </fieldset>
                                        <!-- Bill -->
                                        <fieldset class="fieldset">

                                            <div class="fieldset_container">
                                                <div class="form-card">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2 text-center">Invoice </h2>
                                                    </div>

                                                    <div class="row d-flex justify-content-center" id="bill">
                                                    </div>
                                                    <div class='col-xs-6 col-xs-offset-3'>
                                                        <div class='checkbox'>
                                                            <label>
                                                                <input type='checkbox' name='agree-bill'
                                                                    value='agree'>
                                                                Agree with the terms and conditions
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="button" name="previous-billing"
                                                    class="previous action-button-previous" value="Previous">
                                                <input type="submit" name="next-payment" class="next action-button"
                                                    value="Next">
                                            </div>
                                        </fieldset>
                                        <!--Payment  -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container payment">
                                                <div class="form-card">
                                                    <div class="sec-title">
                                                        <h2 class="fs-title-h2">Payments Information</h2>
                                                    </div>
                                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                        <a class="nav-item" id="nav-home-tab" data-toggle="tab"
                                                            href="#credit-card" role="tab"
                                                            aria-controls="nav-home" aria-selected="true">
                                                            <img src="assets/images/XzOzVHZ.jpg" width="95px"
                                                                height="65px" alt="">
                                                            <input type="radio" name="payment" checked
                                                                value="CreditCard" />
                                                        </a>
                                                        <a class="nav-item " id="vcl-mpesa-tab" data-toggle="tab"
                                                            href="#vcl-mpesa" role="tab"
                                                            aria-controls="vcl-mpesa" aria-selected="false">
                                                            <img src="{{ asset('assets/images/private_candidate_payment/vcl_mpesa.jpg') }}"
                                                                width="115px" height="65px" alt="">
                                                            <input type="radio" name="payment" value="VclMpesa" />
                                                        </a>
                                                        <a class="nav-item " id="ecocash-tab" data-toggle="tab"
                                                            href="#eco-cash" role="tab"
                                                            aria-controls="nav-contact" aria-selected="false">
                                                            <img src="{{ asset('assets/images/private_candidate_payment/EcoCash.png') }}"
                                                                width="95px" height="65px" alt="">
                                                            <input type="radio" name="payment" value="EcoCash" />
                                                        </a>
                                                        {{-- <a class="nav-item " id="nav-cash-deposit-tab" data-toggle="tab"
                                                            href="#cash-deposit" role="tab"
                                                            aria-controls="nav-contact" aria-selected="false">
                                                            <img src="assets/images/private_candidate_payment/deposit.png"
                                                                width="95px" height="65px" alt="">
                                                            <input type="radio" name="payment"
                                                                value="CashDeposit" />
                                                        </a> --}}

                                                    </div>
                                                    <div class="tab-content" id="nav-tabContent">
                                                        <div class="tab-pane fade" id="credit-card" role="tabpanel"
                                                            aria-labelledby="redit-card">
                                                            <div class="form-group">
                                                                <p>Welcome to ECoL secure online payment. Proof of
                                                                    payment will be send to your email on successful
                                                                    payment</p>
                                                                <p>Don't hesitate to contact us for any concerns!</p>
                                                                <a id="iveri-litebox-button">Pay now</a>
                                                                <!-- Iveri start-->
                                                                <input type="hidden" value="Mr."
                                                                    readonly="readonly"
                                                                    name="Ecom_BillTo_Postal_Name_Prefix"
                                                                    id="Ecom_BillTo_Postal_Name_Prefix"
                                                                    class="clsInputReadOnlyText" />
                                                                <input name="Ecom_BillTo_Postal_Name_First"
                                                                    readonly="readonly" type="hidden" value="John"
                                                                    id="Ecom_BillTo_Postal_Name_First"
                                                                    class="clsInputReadOnlyText" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_Name_Middle"
                                                                    id="Ecom_BillTo_Postal_Name_Middle" />

                                                                <input name="Ecom_BillTo_Postal_Name_Last"
                                                                    readonly="readonly" type="hidden" value="Doe"
                                                                    style="width: 20px;"
                                                                    id="Ecom_BillTo_Postal_Name_Last"
                                                                    class="clsInputReadOnlyText" />
                                                                <input name="Ecom_BillTo_Online_Email"
                                                                    readonly="readonly" type="hidden"
                                                                    value="jdoe@mail.com" maxlength="50"
                                                                    id="Ecom_BillTo_Online_Email"
                                                                    class="clsInputReadOnlyText" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Postal_Street_Line1"
                                                                    id="Ecom_ShipTo_Postal_Street_Line1"
                                                                    value="50 Sunny Drive Avenue" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Postal_Street_Line2"
                                                                    id="Ecom_ShipTo_Postal_Street_Line2"
                                                                    value="Sunsetville" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Postal_City"
                                                                    id="Ecom_ShipTo_Postal_City"
                                                                    value="Johannesburg" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Postal_StateProv"
                                                                    id="Ecom_ShipTo_Postal_StateProv"
                                                                    value="Gauteng" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Postal_PostalCode"
                                                                    id="Ecom_ShipTo_Postal_PostalCode"
                                                                    value="2185" />

                                                                <input name="Ecom_ConsumerOrderID" readonly="readonly"
                                                                    type="hidden" value="AUTOGENERATE"
                                                                    maxlength="20" id="Ecom_ConsumerOrderID"
                                                                    class="clsInputReadOnlyText" />
                                                                <input type="hidden" name="Ecom_SchemaVersion"
                                                                    id="Ecom_SchemaVersion" />
                                                                <input type="hidden" name="Ecom_TransactionComplete"
                                                                    id="Ecom_TransactionComplete" value="false" />
                                                                <input type="hidden" name="Lite_Authorisation"
                                                                    id="Lite_Authorisation" value="false" />
                                                                <input type="hidden" name="Lite_Version"
                                                                    id="Lite_Version" value="2.0" />
                                                                <!-- Ecml end-->

                                                                <!-- Lite_Order_LineItems_Product -->
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Lite_Order_LineItems_Product_1"
                                                                    id="Lite_Order_LineItems_Product_1"
                                                                    value="Total Subjects" />
                                                                <input type="hidden" readonly="readonly"
                                                                    name="Lite_Order_LineItems_Quantity_1"
                                                                    id="Lite_Order_LineItems_Quantity_1"
                                                                    value="1" />

                                                                <!-- Lite_Order_LineItems_Amount -->
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Transaction_LineItems_Amount_1"
                                                                    id="Transaction_LineItems_Amount_1"
                                                                    value="100" />
                                                                <input type="hidden"
                                                                    name="Lite_Order_LineItems_Amount_1"
                                                                    id="Lite_Order_LineItems_Amount_1"
                                                                    value="100" />


                                                                <!-- Transaction Amount -->
                                                                <input name="Transaction_Amount"
                                                                    id="Transaction_Amount" type="hidden"
                                                                    value="100" class="clsInputText"
                                                                    value="100" />
                                                                <input type="hidden" value="100"
                                                                    name="Lite_Order_Amount" id="Lite_Order_Amount" />


                                                                <!-- Merchant_Application ID -->
                                                                <input name="Merchant_ApplicationID" type="hidden"
                                                                    value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                                                    maxlength="40" id="Merchant_ApplicationID"
                                                                    class="clsInputText" />
                                                                <input type="hidden"
                                                                    name="Lite_Merchant_ApplicationID"
                                                                    value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                                                    id="Lite_Merchant_ApplicationID" />


                                                                <input type="hidden"
                                                                    name="Ecom_Payment_Card_Protocols"
                                                                    id="Ecom_Payment_Card_Protocols" value="iVeri" />

                                                                <!-- Other Optional fields that can be set -->
                                                                <input type="hidden" name="Lite_Order_Terminal"
                                                                    id="Lite_Order_Terminal" value="77777001" />

                                                                <input type="hidden"
                                                                    name="Lite_Order_AuthorisationCode"
                                                                    id="Lite_Order_AuthorisationCode" />
                                                                <input type="hidden" name="Lite_Website_TextColor"
                                                                    id="Lite_Website_TextColor" value="#ffffff" />
                                                                <input type="hidden" name="Lite_Website_BGColor"
                                                                    id="Lite_Website_BGColor" value="#fff" />
                                                                <input type="hidden"
                                                                    name="Lite_ConsumerOrderID_PreFix"
                                                                    id="Lite_ConsumerOrderID_PreFix" value="LITE" />

                                                                <input type="hidden"
                                                                    name="Lite_Website_Successful_Url"
                                                                    id="Lite_Website_Successful_Url"
                                                                    value="https://examples.iveri.net/Lite/Result.asp" />
                                                                <input type="hidden" name="Lite_Website_Fail_Url"
                                                                    id="Lite_Website_Fail_Url"
                                                                    value="https://examples.iveri.net/Lite/Result.asp" />
                                                                <input type="hidden" name="Lite_Website_Error_Url"
                                                                    id="Lite_Website_Error_Url"
                                                                    value="https://examples.iveri.net/Lite/Result.asp" />
                                                                <input type="hidden" name="Lite_Website_Trylater_Url"
                                                                    id="Lite_Website_Trylater_Url"
                                                                    value="https://examples.iveri.net/Lite/Result.asp" />


                                                                <!-- Ecml start-->

                                                                <!-- ShipTo Additional tags -->
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Name_Prefix"
                                                                    id="Ecom_ShipTo_Postal_Name_Prefix" />
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Name_First"
                                                                    id="Ecom_ShipTo_Postal_Name_First" />
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Name_Middle"
                                                                    id="Ecom_ShipTo_Postal_Name_Middle" />
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Name_Last"
                                                                    id="Ecom_ShipTo_Postal_Name_Last" />
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Name_Suffix"
                                                                    id="Ecom_ShipTo_Postal_Name_Suffix" />

                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_Street_Line3"
                                                                    id="Ecom_ShipTo_Postal_Street_Line3" />
                                                                <input type="hidden"
                                                                    name="Ecom_ShipTo_Postal_CountryCode"
                                                                    id="Ecom_ShipTo_Postal_CountryCode" />
                                                                <input type="hidden" readonly="readonly"
                                                                    class="clsInputReadOnlyText"
                                                                    name="Ecom_ShipTo_Telecom_Phone_Number"
                                                                    id="Ecom_ShipTo_Telecom_Phone_Number" />

                                                                <input type="hidden" name="Ecom_ShipTo_Online_Email"
                                                                    id="Ecom_ShipTo_Online_Email" />

                                                                <!-- ReceiptTo -->
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Name_Prefix"
                                                                    id="Ecom_ReceiptTo_Postal_Name_Prefix" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Name_First"
                                                                    id="Ecom_ReceiptTo_Postal_Name_First" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Name_Middle"
                                                                    id="Ecom_ReceiptTo_Postal_Name_Middle" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Name_Last"
                                                                    id="Ecom_ReceiptTo_Postal_Name_Last" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Name_Suffix"
                                                                    id="Ecom_ReceiptTo_Postal_Name_Suffix" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Street_Line1"
                                                                    id="Ecom_ReceiptTo_Postal_Street_Line1" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Street_Line2"
                                                                    id="Ecom_ReceiptTo_Postal_Street_Line2" />

                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_Street_Line3"
                                                                    id="Ecom_ReceiptTo_Postal_Street_Line3" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_City"
                                                                    id="Ecom_ReceiptTo_Postal_City" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_StateProv"
                                                                    id="Ecom_ReceiptTo_Postal_StateProv" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_PostalCode"
                                                                    id="Ecom_ReceiptTo_Postal_PostalCode" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Postal_CountryCode"
                                                                    id="Ecom_ReceiptTo_Postal_CountryCode" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Telecom_Phone_Number"
                                                                    id="Ecom_ReceiptTo_Telecom_Phone_Number" />
                                                                <input type="hidden"
                                                                    name="Ecom_ReceiptTo_Online_Email"
                                                                    id="Ecom_ReceiptTo_Online_Email" />

                                                                <!-- BillTo -->
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_Name_Suffix"
                                                                    id="Ecom_BillTo_Postal_Name_Suffix" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_Street_Line1"
                                                                    id="Ecom_BillTo_Postal_Street_Line1" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_Street_Line2"
                                                                    id="Ecom_BillTo_Postal_Street_Line2" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_Street_Line3"
                                                                    id="Ecom_BillTo_Postal_Street_Line3" />
                                                                <input type="hidden" name="Ecom_BillTo_Postal_City"
                                                                    id="Ecom_BillTo_Postal_City" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_StateProv"
                                                                    id="Ecom_BillTo_Postal_StateProv" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_PostalCode"
                                                                    id="Ecom_BillTo_Postal_PostalCode" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Postal_CountryCode"
                                                                    id="Ecom_BillTo_Postal_CountryCode" />
                                                                <input type="hidden"
                                                                    name="Ecom_BillTo_Telecom_Phone_Number"
                                                                    id="Ecom_BillTo_Telecom_Phone_Number" />
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="vcl-mpesa" role="tabpanel"
                                                            aria-labelledby="nav-profile-tab">
                                                            <h2>M-PESA</h2>
                                                            <p>Welcome to ECoL secure online payment. Proof of payment
                                                                will be send to your mobile phone on successful payment
                                                            </p>
                                                            <p>Don't hesitate to contact us for any concerns!</p>
                                                            <div class="form-group">
                                                                <label for="mpesa_mobile">Phone number</label>
                                                                <input type="text" name="mpesa_mobile"
                                                                    class="form-control" id="mpesa_mobile"
                                                                    placeholder="*Phone number">

                                                            </div>
                                                            <a href="javascript:void(0)" id="mpesa_pay"
                                                                class="btn btn-primary">Pay now </a>
                                                        </div>
                                                        <div class="tab-pane fade" id="eco-cash" role="tabpanel"
                                                            aria-labelledby="nav-profile-tab">
                                                            <h2>EcoCash</h2>
                                                            <p>Welcome to ECoL secure online payment. Proof of payment
                                                                will be send to your email on successful payment</p>
                                                            <p>Don't hesitate to contact us for any concerns!</p>
                                                            <div class="form-group">
                                                                <label for="ecocash_mobile">Phone number</label>
                                                                <input type="text" name="ecocash_mobile"
                                                                    class="form-control" id="ecocash_mobile"
                                                                    placeholder="*Phone number">

                                                            </div>
                                                            <a href="javascript:void(0)" id="ecocash_pay"
                                                                class="btn btn-primary">Pay now </a>
                                                        </div>
                                                        <div class="tab-pane fade" id="cash-deposit" role="tabpanel"
                                                            aria-labelledby="nav-contact-tab">
                                                            <h2>Cash Deposit</h2>
                                                            <p>Welcome to ECoL secure online payment. Upload of payment
                                                                from the bank
                                                            </p>
                                                            <p>Don't hesitate to contact us for any concerns!</p>
                                                            <button type="button"
                                                                class="btn btn-primary modalTrigger"
                                                                data-toggle="modal" data-target="#confirmationModal">
                                                                Upload proof of payment
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="button" name="previous"
                                                    class="previous action-button-previous" value="Previous">
                                                <input type="submit" name="make_payment" class="next action-button"
                                                    disabled value="Confirm">
                                            </div>
                                        </fieldset>
                                        <!-- Finish -->
                                        <fieldset class="fieldset">
                                            <div class="fieldset_container">
                                                <div class="form-card">
                                                    <div class="row justify-content-center">
                                                        <div> <img
                                                                src="https://img.icons8.com/color/96/000000/ok--v2.png"
                                                                class="fit-image"> </div>
                                                    </div>
                                                    <br><br>
                                                    <div class="row justify-content-center">
                                                        <div class="col-md-8 text-center">
                                                            <div id="timetable"></div>
                                                            <h5 class="success-payment">Your payment has been processed
                                                                and your registration is
                                                                successful!</h5>
                                                            <h6 class="download-type">Download or email your
                                                                TIMETABLE
                                                            </h6>
                                                            <div class="timetable-btns">
                                                                <a href=""
                                                                    class="btn btn-primary download-timetable"><i
                                                                        class="fa fa-download "></i> Download</a>
                                                                <a href=""
                                                                    class="btn btn-primary send-email"><i
                                                                        class=" far fa-paper-alternativee"></i> Send to
                                                                    email</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end MultiStep Form -->
    </main>


    <div class="disclaimer-overlay">
        <div class="disclaimer">
            <h2 class="disclaimer-title">Terms and Conditions </h2>
            <div class="disclaimer-message">
                <h5>By engaging with ECoL regarding your examinations, you agree to the terms outlined below:</h5>
                <ol>
                    <li>Introduction
                        <ul>
                            <p>The Examinations Council of Lesotho (ECoL) is committed to protecting your personal
                                information.</p>
                        </ul>
                    </li>
                    <li>
                        Collection and Use of Personal Information
                        <p>ECoL collects and processes your personal information solely for the purposes of
                            administering,
                            processing, and calculating your examination results. This may include, but is not limited
                            to.</p>
                        <ul>
                            <li>Your name, surname, and identification details.</li>
                            <li>Examination registration details.</li>
                            <li>Examination scores and performance data.</li>
                        </ul>
                    </li>


                    <li>
                        Sharing of Personal Information
                        <p>Your personal information will only be shared with third parties who are.</p>
                        <ul>
                            <li>Formal ECoL partners.</li>
                            <li>Drectly involved in the processing and calculation of examination results.</li>
                            <li>Bound by confidentiality and data protection agreements</li>
                            <p>ECoL will not sell, distribute, or disclose your personal information to unauthorized
                                third parties</p>
                        </ul>
                    </li>


                    <li>
                        Data Protection, Security and Compliance
                        <p>CoL takes reasonable technical and administrative measures to protect your personal
                            information against unauthorized access, loss, or misuse.
                            ECoL adheres to internationally recognized security and quality standards, including:</p>
                        <ul>
                            <li>ISO/IEC 27001:2013 – Information Security Management System (ISMS) certification.</li>
                            <li>ISO 9001: 2015 – Quality Management System (QMS) certification.</li>
                            <li>Bound by confidentiality and data protection agreements</li>
                            <p>These certifications ensure that your personal data is managed securely and processed
                                with high-quality standards.</p>
                        </ul>
                    </li>


                    <li>
                        Your Rights
                        <p>You have the right to:</p>
                        <ul>
                            <li>Request access to your personal information.</li>
                            <li>Request correction of inaccurate information.</li>
                            <li>Object to the processing of your data under certain circumstances.</li>
                            <li>Decline and not register for examinations.</li>
                        </ul>
                    </li>
                    <li>
                        Retention of Personal Information
                        <p>Your personal information will be retained for as long as necessary to fulfill the purposes
                            outlined in this document and in accordance with applicable laws.</p>

                    </li>

                    <li>
                        Contact Information
                        <p>For any inquiries or concerns regarding the processing of your personal information, please
                            contact ECoL through official communication channels.</p>

                    </li>
                </ol>
            </div>
            <div class="disclaimer-buttons">
                <button class="agree-btn" disabled>I Agree</button>
                <button class="decline-btn">Decline</button>
            </div>
            <div class="decline-message">
                Access to this website has been denied. Thank you for understanding.
            </div>
        </div>
    </div>

    <!-- Modal HTML iveri -->
    <div id="iveri-litebox" class="center-block"></div>
    <!-- Modal HTML confirrmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="indicator"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Proof of Payments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="bankConfirmationForm" method="post" enctype="multipart/form-data">
                        <p>Please upload valid Proof of Payments For Your Registration to be Successfully</p>

                        <p>Must be image or picture</p>
                        <p>Must be readable</p>

                        <div class="form-group">
                            <label for="bank_confirmation">Bank Confirmation</label>
                            <input class="form-control" id='bank_confirmation' name="bank_confirmation"
                                type='file' onchange="proccess(window.lastFile=this.files[0])" />
                        </div>
                        <div class="bank-confimation">

                        </div>
                    </form>
                </div>
                <div class="row col-md-12 ml-auto mr-auto preview"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <span class="btn btn-info btn-sm" id="upload-confirmation">Submit</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal HTML confirrmation -->
    <div class="modal fade" id="confirmationBalanceModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationBalanceModal" aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="indicator"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Proof of Payments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="bankConfirmationBalanceForm" method="post"
                        enctype="multipart/form-data">
                        <p>Please upload valid proof of payment to complete the registration.</p>
                        <div class="form-group">
                            <label for="bank_confirmation_balance">Bank confirmation balance</label>
                            <input class="form-control" id='bank_confirmation_balance'
                                onchange="proccess(window.lastFile=this.files[0])" name="bank_confirmation_balance"
                                type='file' />
                        </div>
                        <div class="bank-confimation">

                        </div>
                    </form>
                </div>
                <div class="row col-md-12 ml-auto mr-auto preview"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <span class="btn btn-info btn-sm" id="upload-confirmation-balance">Submit</span>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <!--  Notifications Plugin  toastr  -->
    <script src="{{ asset('school/assets/js/toastr.min.js') }}"></script>
    <script>
        // Disclaimer
        $(document).on("click", ".agree-btn", function() {
            $('.disclaimer-overlay').css({
                "display": "none"
            });
        });

        $(document).on("click", ".decline-btn", function() {
            location.href = "/";
        });

        $('.disclaimer-message').scroll(function() {
            if ($(this).scrollTop() + $(this).height() >= $(this)[0].scrollHeight - 100) {
                $('.agree-btn').attr('disabled', null);
            }
        });





        /*****  Display candidates*******/

        /*****  Display candidates*******/
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "10000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }



        $(document).on("click", "#mpesa_pay", function() {
            vclMpesaComplete($(this));
        });

        $(document).on("click", "#ecocash_pay", function() {
            ecoCashComplete($(this));
        });

        // Cash Deposit upload-confirmation
        $(document).on("click", "#upload-confirmation", function() {
            cashDepositComplete($(this));
        });

        // Cash Deposit  upload-confirmation-balance
        $(document).on("click", "#upload-confirmation-balance", function() {
            cashDepositBalance($(this));
        });

        // lite Box
        function liteboxComplete(data) {
            var inputData = $("#msform").serialize();
            var liteInputs = $.parseJSON(data);
            var liteData = "";

            for (const fieldname in liteInputs) {
                liteData += `&${fieldname }=${liteInputs[fieldname ]}`;
            }
            inputData += liteData
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "{{ route('transaction') }}",
                method: "POST",
                cache: false,
                data: inputData,
                success: function(data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        if (data.status == 1) {
                            $(".modal-backdrop").hide();
                            $('input[name="make_payment"]').prop("disabled", false);
                            $('input[name="make_payment"]').trigger("click");
                            if (data.publised) {
                                $(' timetable-btns').show();
                                $(".download-timetable").prop(
                                    "href",
                                    "print-timetable?centre_no=" +
                                    data.output['centreNo'] +
                                    "&candidate_no=" +
                                    data.output['candidate_no'] +
                                    "&session=" + data.output['session'] +
                                    "&level=" + data.output['level'] +
                                    "&download=1"
                                );
                                $(".send-email").prop(
                                    "href",
                                    "print-timetable?centre_no=" + data.output['centreNo'] +
                                    "&candidate_no=" + data.output['candidate_no'] +
                                    "&session=" + data.output['session'] +
                                    "&level=" + data.output['level'] +
                                    "&download=1&send=1"
                                );

                            } else {
                                // download-type
                                $(".download-type").html(
                                    "You will get your Timetable once officially published ")
                                // timetable-btns
                                $('.timetable-btns').hide();
                            }

                        } else {
                            $('.payement-error').show();
                            $(".modal-backdrop").hide();
                            $(".payement-error").find("ul").html('');
                            $(".payement-error").css('display', 'block');
                            $(".payement-error").find("ul").append(
                                "<li>Dear valued customer, your transaction has declined! Please try again,<a href='//private-candidate'>reregister</a></li>"
                            );
                            $('#iveri-litebox-button').hide();
                        }
                    } else {
                        printErrorMsg('#msform', data.errors)
                    }
                }
            });
        }

        // 	ECocash
        function ecoCashComplete(element) {
            var inputData = $("#msform").serialize();
            //    M-Pesa request
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            var caption = element.html();
            $.ajax({
                url: "{{ route('transaction') }}",
                method: "POST",
                data: inputData,
                beforeSend: function() {
                    element.prop('disabled', true).html("Processing.....");

                },
            }).done(function(data) {
                var data = isJsonString(data) ? $.parseJSON(data) : data;
                console.log(data);
                element.prop('disabled', false).html(caption);
                if ($.isEmptyObject(data.errors)) {
                    // var data = $.parseJSON(data);
                    element.prop('disabled', false).html(caption);
                    if (data.status == 1) {
                        $('input[name="make_payment"]').prop("disabled", false);
                        $('input[name="make_payment"]').trigger("click");
                        if (data.publised) {
                            $(' timetable-btns').show();
                            $(".download-timetable").prop(
                                "href",
                                "print-timetable?centre_no=" +
                                data.output['centreNo'] +
                                "&candidate_no=" +
                                data.output['candidate_no'] +
                                "&session=" + data.output['session'] +
                                "&level=" + data.output['level'] +
                                "&download=1"
                            );
                            $(".send-email").prop(
                                "href",
                                "print-timetable?centre_no=" + data.output['centreNo'] +
                                "&candidate_no=" + data.output['candidate_no'] +
                                "&session=" + data.output['session'] +
                                "&level=" + data.output['level'] +
                                "&download=1&send=1"
                            );


                        } else {
                            // download-type
                            $(".download-type").html(
                                "You will get your Timetable once officially published ")
                            // timetable-btns
                            $('.timetable-btns').hide();
                        }

                    }
                } else {
                    printErrorMsg('#msform', data.errors)
                    element.prop('disabled', false).html(caption);
                }

            }).fail(function(xhr, status, error) {
                element.prop('disabled', false).html(caption);
            });
        }
        // MPESA
        function vclMpesaComplete(element) {
            var inputData = $("#msform").serialize();
            //    M-Pesa request
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            var caption = element.html();
            $.ajax({
                url: "{{ route('transaction') }}",
                method: "POST",
                data: inputData,
                beforeSend: function() {
                    element.prop('disabled', true).html("Processing.....");

                },
            }).done(function(data) {
                var data = isJsonString(data) ? $.parseJSON(data) : data;
                console.log(data);
                element.prop('disabled', false).html(caption);
                if ($.isEmptyObject(data.errors)) {
                    // var data = $.parseJSON(data);
                    element.prop('disabled', false).html(caption);
                    if (data.status == 1) {
                        $('input[name="make_payment"]').prop("disabled", false);
                        $('input[name="make_payment"]').trigger("click");
                        if (data.publised) {
                            $(' timetable-btns').show();
                            $(".download-timetable").prop(
                                "href",
                                "print-timetable?centre_no=" +
                                data.output['centreNo'] +
                                "&candidate_no=" +
                                data.output['candidate_no'] +
                                "&session=" + data.output['session'] +
                                "&level=" + data.output['level'] +
                                "&download=1"
                            );
                            $(".send-email").prop(
                                "href",
                                "print-timetable?centre_no=" + data.output['centreNo'] +
                                "&candidate_no=" + data.output['candidate_no'] +
                                "&session=" + data.output['session'] +
                                "&level=" + data.output['level'] +
                                "&download=1&send=1"
                            );


                        } else {
                            // download-type
                            $(".download-type").html(
                                "You will get your Timetable once officially published ")
                            // timetable-btns
                            $('.timetable-btns').hide();
                        }

                    }
                } else {
                    printErrorMsg('#msform', data.errors)
                    element.prop('disabled', false).html(caption);
                }

            }).fail(function(xhr, status, error) {
                element.prop('disabled', false).html(caption);
            });
        }

        // Cash Deposit
        function cashDepositComplete(element) {
            var candidateNo = $("input[name='candidate_No']").val();
            var surname = $("input[name='surname']").val();
            var other_name = $("input[name='other_name']").val();
            var gender = $("input[name='gender']").val();
            var email_Address = $("input[name='email_Address']").val();
            var phone_No = $("input[name='phone_No']").val();
            var payment = $("input[name='payment']:checked").val();
            var centreNo = $("input[name='centreNo']").val();
            var increaseSubjects = getCheckedBoxes("input[type='checkbox'][name='increaseSubjects']");
            var Session = $("#session").find(":selected").val();
            var level = $("#level").find(":selected").val();
            var subject = getCheckedBoxes("input[type='checkbox'][name='subject[]']");
            var mathematics = getCheckedBoxes("input[type='checkbox'][name='mathematics[]']");
            var physcial_science = getCheckedBoxes("input[type='checkbox'][name='physcial_science[]']");
            var number_of_subjects = $("input[name='number_of_subjects']").val();
            var totalAmount = $("input[name='total_amount']").val();
            totalAmount = parseFloat(totalAmount).toFixed(2);
            var jsonData = [];


            jsonData['candidateNo'] = candidateNo;
            jsonData['surname'] = surname;
            jsonData['other_name'] = other_name;
            jsonData['email_Address'] = email_Address;
            jsonData['phone_No'] = phone_No;
            jsonData['payment'] = payment;
            jsonData['centreNo'] = centreNo;
            jsonData['increaseSubjects'] = increaseSubjects;
            jsonData['Session'] = Session;
            jsonData['level'] = level;
            jsonData['subject'] = subject
            jsonData['mathematics'] = mathematics;
            jsonData['physcial_science'] = physcial_science;
            jsonData['number_of_subjects'] = number_of_subjects;
            jsonData['total_amount'] = totalAmount;

            var formData = new FormData();
            //Form data
            for (const key in jsonData) {
                const value = jsonData[key];
                formData.append(key, value);

            }

            //File data
            formData.append("bank_confirmation", $('#bankConfirmationForm input[type="file"]')[0].files[0]);
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            var caption = element.html();

            $.ajax({
                url: "{{ route('transaction') }}",
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    element.prop('disabled', true).html("Processing.....");
                },
                success: function(response) {
                    // ecocash response
                    var data = response;

                    console.log(data);


                    if ($.isEmptyObject(data.errors)) {
                        if (data.status == 1) {
                            $("#confirmationModal").modal("hide");
                            $('input[name="make_payment"]').prop("disabled", false);
                            $('input[name="make_payment"]').trigger("click");

                            $("#bank_confirmation").val('');
                            if (data.publised && data.status == 2) {
                                $(' timetable-btns').show();
                                $(".download-timetable").prop(
                                    "href",
                                    "print-timetable?centreNo=" +
                                    data.output['centreNo'] +
                                    "&candidateNo=" +
                                    data.output['candidateNo'] +
                                    "&download=1"
                                );
                                $(".send-email").prop(
                                    "href",
                                    "print-timetable?centreNo=" +
                                    data.output['centreNo'] +
                                    "&candidateNo=" +
                                    data.output['candidateNo'] +
                                    "&download=1&send=1"
                                );

                            } else {
                                // download-type
                                $(".success-payment").html(
                                    "Your proof of payment  has been processed  and send successfully!");
                                $(".download-type").html(
                                    "You will get your Timetable once officially published and  your payment approved successfully"
                                );
                                // timetable-btns
                                $('.timetable-btns').hide();
                            }

                        }
                    } else {
                        $("#confirmationModal").modal("hide");


                        $("#bank_confirmation").val('');

                        printErrorMsg('#msform', data.errors)
                    }

                },
                complete: function(data) {
                    element.prop('disabled', false).html(caption);
                }
            });
        }

        // Cash Deposit balance
        function cashDepositBalance(element) {
            var candidateNo = $("input[name='candidate_No']").val();
            var jsonData = [];
            jsonData['candidateNo'] = candidateNo;


            var formData = new FormData();
            //Form data
            for (const key in jsonData) {
                const value = jsonData[key];
                formData.append(key, value);

            }

            //File data
            formData.append("bank_confirmation", $('#bankConfirmationBalanceForm input[type="file"]')[0].files[0]);
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            var caption = element.html();

            $.ajax({
                url: "{{ route('balance') }}",
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function() {
                    element.prop('disabled', true).html("Processing.....");

                },
                success: function(response) {
                    // ecocash response
                    var data = response;



                    if ($.isEmptyObject(data.errors)) {
                        if (data.status == 1) {
                            $("#confirmationBalanceModal").modal("hide");
                            element.prop('disabled', false).html(caption);
                            $("#bank_confirmation_balance").val('');
                            swal({
                                title: "Success!",
                                text: "You have Successfully uploaded proof of payment!",
                                icon: "success",
                            });
                        }
                    } else {
                        $("#confirmationModal").modal("hide");
                        element.prop('disabled', false).html(caption);
                        $("#bank_confirmation_balance").val('');

                        printErrorMsg('#msform', data.errors)

                    }

                }
            });
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

        function getCheckedBoxes(input) {
            var checkboxesChecked = [];
            $(input + ":checked").each(function() {
                checkboxesChecked.push($(this).val());
            });
            return checkboxesChecked;
        }
    </script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/additional-methods.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
