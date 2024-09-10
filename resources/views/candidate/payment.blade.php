@extends('layouts.candidate')
<script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.min.js"></script>
<script src="https://portal.nedsecure.co.za/scripts/bootstrap/js/bootstrap.min.js"></script>
<script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>
<script>
    liteboxInitialise('https://portal.nedsecure.co.za', "msform");
</script>
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ date('Y') }}
                            Examinations
                            Registration: <i>{{ auth()->user()->username }} {{ auth()->user()->national_id }}</i>
                        </h4>
                        <!-- MultiStep Form -->
                        <section id="grad1">
                            <div class="container">
                                <div class="row justify-content-center mt-0">

                                    <div class="col-md-9 text-center p-0 mt-3 mb-2">
                                        <div class="card">

                                            <!-- <p>Fill all form field to go to next step</p> -->
                                            <div class="row justify-content-center">
                                                <div class="col-md-12">
                                                    <form action="" method="post" id="msform">
                                                        @csrf
                                                        <!-- progressbar -->
                                                        <div class="progressbar">

                                                            <ul id="progressbar_content">
                                                                <li class="active" id="invoice">
                                                                    <strong><i class="fas fa-receipt"></i>Personal
                                                                        Info</strong>
                                                                </li>
                                                                <li id="nextKin"><strong><i
                                                                            class="far fa-credit-card"></i>Next of
                                                                        Kin</strong>
                                                                </li>
                                                                <li id="payment"><strong><i
                                                                            class="far fa-credit-card"></i>Payment</strong>
                                                                </li>
                                                                <li id="confirm"><strong><i
                                                                            class="fas fa-check"></i>Finish</strong>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <fieldset id="candidate-information">
                                                            <div class="fieldset_container">
                                                                <div class="form-card">
                                                                    <div class="invoice-wrapper">
                                                                        <div class="invoice-top">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="invoice-top-left">
                                                                                        <h2 class="client-company-name">
                                                                                            Examinations
                                                                                            Council of
                                                                                            Lesotho
                                                                                        </h2>
                                                                                        <h6 class="client-address">
                                                                                            {{ auth()->user()->center_no }}
                                                                                            {{ $subjects->first()->center_name }}
                                                                                        </h6>
                                                                                        <h6><b>Total Fee</b>
                                                                                            LSL
                                                                                            {{ number_format((float) $total_amount, 2, '.', '') }}
                                                                                        </h6>
                                                                                        <h3 class="headline">Personal</h3>
                                                                                        <input type="hidden"
                                                                                            name="center_no"
                                                                                            value="{{ $candidate->center_no }}">
                                                                                        <input type="hidden"
                                                                                            name="national_id"
                                                                                            value="{{ $candidate->national_id }}">
                                                                                        <input type="hidden"
                                                                                            name="candidate_no"
                                                                                            value="{{ $candidate->candidate_no}}">
                                                                                        <input type="hidden"
                                                                                            name="candidate_surname"
                                                                                            value="{{ $candidate->candidate_surname }}">
                                                                                        <input type="hidden"
                                                                                            name="candidate_other_name"
                                                                                            value="{{ $candidate->candidate_other_name }}">
                                                                                        <input type="hidden" name="gender"
                                                                                            value="{{ $candidate->gender }}">
                                                                                        <input type="hidden"
                                                                                            name="number_of_subjects"
                                                                                            value="{{ $candidate->subject_number }}">

                                                                                        <input type="hidden" name="session"
                                                                                            value="{{ $candidate->session }}">
                                                                                        <input type="hidden"
                                                                                            name="financial_year"
                                                                                            value="{{ $candidate->financial_year }}">
                                                                                        <input type="hidden"
                                                                                            name="total_amount"
                                                                                            value="{{ $total_amount }}">
                                                                                        <input type="hidden" name="level"
                                                                                            value="{{ $candidate->level }}">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="candidate_email">Email</label>
                                                                                            <input type="text"
                                                                                                class="form-control  form-control-sm "
                                                                                                id="candidate_email"
                                                                                                name="candidate_email"
                                                                                                placeholder="Email Address">
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="cadidate_phone">Phone
                                                                                                Number</label>
                                                                                            <input type="text"
                                                                                                class="form-control form-control-sm"
                                                                                                id="candidate_phone_number"
                                                                                                name="candidate_phone_number"
                                                                                                placeholder="Phone Number">
                                                                                        </div>
                                                                                        <div class="form-group col-12">
                                                                                            <label
                                                                                                for="special_need">Special
                                                                                                needs</label>
                                                                                            <select name="special_need"
                                                                                                class="form-control form-control-sm"
                                                                                                id="special_need">
                                                                                                <option value="">
                                                                                                    Please select special
                                                                                                    need(s)</option>
                                                                                                @foreach ($specialNeeds as $specialNeed)
                                                                                                    <option
                                                                                                        value="{{ $specialNeed->id }}">
                                                                                                        {{ $specialNeed->name }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <h3 class="headline">Address</h3>
                                                                                        <div class="row">
                                                                                            <div class="form-group col-6">
                                                                                                <label
                                                                                                    for="candidate_postal_address">Postal
                                                                                                    Address </label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    id="candidate_postal_address"
                                                                                                    name="candidate_postal_address"
                                                                                                    placeholder="P.O.Box 2398">

                                                                                            </div>
                                                                                            <div class="form-group col-6">
                                                                                                <label
                                                                                                    for="candidate_physical_address">Physical
                                                                                                    Address</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm "
                                                                                                    id="candidate_physical_address"
                                                                                                    name="candidate_physical_address"
                                                                                                    placeholder="Qoaling">
                                                                                            </div>

                                                                                            <div class="form-group col-6">
                                                                                                <label
                                                                                                    for="candidate_village">Village</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    id="candidate_village"
                                                                                                    name="candidate_village"
                                                                                                    placeholder="Ha Seoli">
                                                                                            </div>
                                                                                            <div class="form-group col-6">
                                                                                                <label
                                                                                                    for="candidate_district">District</label>
                                                                                                <select
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="candidate_district"
                                                                                                    id="candidate_district">
                                                                                                    <option value="">
                                                                                                        Please Select
                                                                                                        District</option>
                                                                                                    @foreach ($districts as $district)
                                                                                                        <option
                                                                                                            value="{{ $district->district }}">
                                                                                                            {{ $district->district }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                        <div class="invoice-bottom">
                                                                            <div class="row">
                                                                                <div class="clearfix">
                                                                                </div>
                                                                                <div class="clearfix">
                                                                                </div>
                                                                                <div class="col-xs-12">
                                                                                    <hr class="divider">
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <h6 class="text-left">
                                                                                        support@ecol.org.ls
                                                                                    </h6>
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <h6 class="text-center">
                                                                                        +266 2231 2880
                                                                                    </h6>
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <h6 class="text-right">
                                                                                        +266
                                                                                        2231 2880</h6>
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <h6 class="text-right">
                                                                                        +266
                                                                                        2231 28801</h6>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <input type="submit" name="next"
                                                                    class="next action-button" value="Next">

                                                            </div>
                                                        </fieldset>

                                                        <fieldset id="candidate-guardian">
                                                            <div class="fieldset_container">
                                                                <div class="form-card">
                                                                    <h3 class="headline">Personal Information</h3>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="guardian_type">Relationship
                                                                                Between</label>
                                                                            <select name="guardian_type"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_type">
                                                                                <option value="">Please select
                                                                                    relationship</option>
                                                                                @foreach ($guardian_types as $guardian_type)
                                                                                    <option
                                                                                        value="{{ $guardian_type->id }}">
                                                                                        {{ $guardian_type->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="guardian_national_id">National
                                                                                Id</label>
                                                                            <input type="text"
                                                                                class="form-control  form-control-sm "
                                                                                id="guardian_national_id"
                                                                                name="guardian_national_id"
                                                                                placeholder="national id">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_name">Other
                                                                                Names</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_name" name="guardian_name"
                                                                                placeholder="Name">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_surname">Surname</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_surname"
                                                                                name="guardian_surname"
                                                                                placeholder="Surname">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_email">Email</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_email" name="guardian_email"
                                                                                placeholder="Email">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_phone">Phone
                                                                                Number</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_phone_number"
                                                                                name="guardian_phone_number"
                                                                                placeholder="Phone Number">
                                                                        </div>
                                                                    </div>
                                                                    <h3 class="headline">Address</h3>
                                                                    <div class="row">
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_postal_address">Postal
                                                                                Address </label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_postal_address"
                                                                                name="guardian_postal_address"
                                                                                placeholder="P.O.Box 2398">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_physical_address">Physical
                                                                                Address</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_physical_address"
                                                                                name="guardian_physical_address"
                                                                                placeholder="Qoaling">
                                                                        </div>

                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_village">Village</label>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm"
                                                                                id="guardian_village"
                                                                                name="guardian_village"
                                                                                placeholder="Ha Seoli">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="guardian_district">District</label>
                                                                            <select class="form-control form-control-sm"
                                                                                name="guardian_district"
                                                                                id="guardian_district">
                                                                                <option value="">Please Select
                                                                                    District</option>
                                                                                @foreach ($districts as $district)
                                                                                    <option
                                                                                        value="{{ $district->district }}">
                                                                                        {{ $district->district }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="button" name="previous"
                                                                    class="previous action-button-previous"
                                                                    value="Previous">
                                                                <input type="submit" name="next"
                                                                    class="next action-button" value="Next">

                                                            </div>
                                                        </fieldset>

                                                        <!--Payment  -->
                                                        <fieldset>
                                                            <div class="fieldset_container payment">
                                                                <div class="payment-methods">
                                                                    <div class="title">Choose a payment method</div>
                                                                    <label class="payment-method basic-payment-method"
                                                                        for="basic">
                                                                        <input type="radio" name="payment"
                                                                            id="basic" value="CreditCard" />
                                                                        <div class="payment-method-content">
                                                                            <img loading="lazy"
                                                                                src="{{ asset('assets/images/XzOzVHZ.jpg') }}"
                                                                                alt="" />
                                                                            <div class="payment-method-details">
                                                                                <span>EFT </span>
                                                                                <p>Card Transactions</p>

                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                    <label class="payment-method complete-payment-method"
                                                                        for="complete">
                                                                        <input type="radio" id="complete"
                                                                            name="payment" value="VclMpesa" />
                                                                        <div class="payment-method-content">
                                                                            <img loading="lazy"
                                                                                src="{{ asset('assets/images/private_candidate_payment/vcl_mpesa.jpg') }}"
                                                                                alt="" />
                                                                            <div class="payment-method-details">
                                                                                <span>M-pesa</span>
                                                                                <p>Mobile money.</p>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                                <div id="payment-tab-content">
                                                                    <div class="payment-tab-pane" id="card">
                                                                        <div class="form-group">
                                                                            <h2>Electronic Fund Transfer</h2>
                                                                            <p>Welcome to ECoL secure online payment. Proof
                                                                                of
                                                                                payment will be send to your email on
                                                                                successful
                                                                                payment</p>
                                                                            <p>Don't hesitate to contact us for any
                                                                                concerns!</p>
                                                                            <a id="iveri-litebox-button">Pay now</a>
                                                                            <!-- Iveri start-->
                                                                            <input type="hidden" value="Mr."
                                                                                readonly="readonly"
                                                                                name="Ecom_BillTo_Postal_Name_Prefix"
                                                                                id="Ecom_BillTo_Postal_Name_Prefix"
                                                                                class="clsInputReadOnlyText" />
                                                                            <input name="Ecom_BillTo_Postal_Name_First"
                                                                                readonly="readonly" type="hidden"
                                                                                value="John"
                                                                                id="Ecom_BillTo_Postal_Name_First"
                                                                                class="clsInputReadOnlyText" />
                                                                            <input type="hidden"
                                                                                name="Ecom_BillTo_Postal_Name_Middle"
                                                                                id="Ecom_BillTo_Postal_Name_Middle" />

                                                                            <input name="Ecom_BillTo_Postal_Name_Last"
                                                                                readonly="readonly" type="hidden"
                                                                                value="Doe" style="width: 20px;"
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

                                                                            <input name="Ecom_ConsumerOrderID"
                                                                                readonly="readonly" type="hidden"
                                                                                value="AUTOGENERATE" maxlength="20"
                                                                                id="Ecom_ConsumerOrderID"
                                                                                class="clsInputReadOnlyText" />
                                                                            <input type="hidden"
                                                                                name="Ecom_SchemaVersion"
                                                                                id="Ecom_SchemaVersion" />
                                                                            <input type="hidden"
                                                                                name="Ecom_TransactionComplete"
                                                                                id="Ecom_TransactionComplete"
                                                                                value="false" />
                                                                            <input type="hidden"
                                                                                name="Lite_Authorisation"
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
                                                                                name="Lite_Order_Amount"
                                                                                id="Lite_Order_Amount" />


                                                                            <!-- Merchant_Application ID -->
                                                                            <input name="Merchant_ApplicationID"
                                                                                type="hidden"
                                                                                value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                                                                maxlength="40" id="Merchant_ApplicationID"
                                                                                class="clsInputText" />
                                                                            <input type="hidden"
                                                                                name="Lite_Merchant_ApplicationID"
                                                                                value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                                                                id="Lite_Merchant_ApplicationID" />


                                                                            <input type="hidden"
                                                                                name="Ecom_Payment_Card_Protocols"
                                                                                id="Ecom_Payment_Card_Protocols"
                                                                                value="iVeri" />

                                                                            <!-- Other Optional fields that can be set -->
                                                                            <input type="hidden"
                                                                                name="Lite_Order_Terminal"
                                                                                id="Lite_Order_Terminal"
                                                                                value="77777001" />

                                                                            <input type="hidden"
                                                                                name="Lite_Order_AuthorisationCode"
                                                                                id="Lite_Order_AuthorisationCode" />
                                                                            <input type="hidden"
                                                                                name="Lite_Website_TextColor"
                                                                                id="Lite_Website_TextColor"
                                                                                value="#ffffff" />
                                                                            <input type="hidden"
                                                                                name="Lite_Website_BGColor"
                                                                                id="Lite_Website_BGColor"
                                                                                value="#fff" />
                                                                            <input type="hidden"
                                                                                name="Lite_ConsumerOrderID_PreFix"
                                                                                id="Lite_ConsumerOrderID_PreFix"
                                                                                value="LITE" />

                                                                            <input type="hidden"
                                                                                name="Lite_Website_Successful_Url"
                                                                                id="Lite_Website_Successful_Url"
                                                                                value="https://examples.iveri.net/Lite/Result.asp" />
                                                                            <input type="hidden"
                                                                                name="Lite_Website_Fail_Url"
                                                                                id="Lite_Website_Fail_Url"
                                                                                value="https://examples.iveri.net/Lite/Result.asp" />
                                                                            <input type="hidden"
                                                                                name="Lite_Website_Error_Url"
                                                                                id="Lite_Website_Error_Url"
                                                                                value="https://examples.iveri.net/Lite/Result.asp" />
                                                                            <input type="hidden"
                                                                                name="Lite_Website_Trylater_Url"
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

                                                                            <input type="hidden"
                                                                                name="Ecom_ShipTo_Online_Email"
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
                                                                            <input type="hidden"
                                                                                name="Ecom_BillTo_Postal_City"
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
                                                                    <div class="payment-tab-pane" id="mpesa">
                                                                        <h2>M-PESA</h2>
                                                                        <p>Welcome to ECoL secure online payment. Proof of
                                                                            payment
                                                                            will be send to your mobile phone on successful
                                                                            payment
                                                                        </p>
                                                                        <p>Don't hesitate to contact us for any concerns!
                                                                        </p>
                                                                        <div class="form-group">
                                                                            <label for="mpesa_mobile">Phone number</label>
                                                                            <input type="text" name="mpesa_mobile"
                                                                                class="form-control" id="mpesa_mobile"
                                                                                placeholder="*Phone number">

                                                                        </div>
                                                                        <a href="javascript:void(0)" id="mpesa_pay"
                                                                            class="btn btn-primary">Pay now </a>
                                                                    </div>

                                                                </div>
                                                                <input type="button" name="previous"
                                                                    class="previous action-button-previous"
                                                                    value="Previous">

                                                                <input type="submit" name="make_payment"
                                                                    class="next action-button" disabled value="Next">
                                                            </div>
                                                        </fieldset>
                                                        <!-- Finish -->
                                                        <fieldset>
                                                            <div class="fieldset_container">
                                                                <div class="form-card">
                                                                    <div class="row justify-content-center">
                                                                        <div
                                                                            class="modalbox success col-sm-12 col-md-8 col-lg-6 center animate">
                                                                            <div class="icon">
                                                                                <span
                                                                                    class="mdi mdi-checkbox-marked-circle"></span>
                                                                            </div>
                                                                            <!--/.icon-->
                                                                            <h5>Your payment has been processed
                                                                                and your registration is
                                                                                successful!!</h5>
                                                                            <p>Download or email your
                                                                                TIMETABLE
                                                                            </p>
                                                                            <h6 class="download-type">Download or email
                                                                                your
                                                                                TIMETABLE
                                                                            </h6>
                                                                            <div class="timetable-btns">
                                                                                <a href=""
                                                                                    class="btn btn-primary download-timetable"><i
                                                                                        class="fa fa-download "></i>
                                                                                    Download</a>
                                                                                <a href=""
                                                                                    class="btn btn-primary send-email"><i
                                                                                        class=" far fa-paper-alternativee"></i>
                                                                                    Send to
                                                                                    email</a>
                                                                            </div>
                                                                        </div>
                                                                        <!--/.success-->
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
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal HTML iveri -->
    <div id="iveri-litebox" class="center-block"></div>
    @push('scripts')
        <script>
            $(document).on("click", "#mpesa_pay", function() {
                vclMpesaComplete($(this));
            });

            $(document).on("click", "#ecocash_pay", function() {
                ecoCashComplete();
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
                    url: "{{ route('candidate.transaction') }}",
                    method: "POST",
                    cache: false,
                    data: inputData,
                    success: function(data) {
                        console.log(data);
                        if ($.isEmptyObject(data.errors)) {
                            if (data.status == 1) {
                                $(".modal-backdrop").hide();
                                $("#iveri-litebox-dialog").hide()
                                $('input[name="make_payment"]').prop("disabled", false);
                                $('input[name="make_payment"]').trigger("click");
                                if (data.publised) {
                                    $('.timetable-btns').show();
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
            function ecoCashComplete() {
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

                // ECOCASH
                // var ecocash_vendor_code = $("input[name='ecocash_vendor_code']").val();
                // var ecocash_key = $("input[name='ecocash_key']").val();
                var ecocash_mobile = $("input[name='ecocash_mobile']").val();
                // var ecocash_Checksum = $("input[name='ecocash_Checksum']").val();
                var ecocash_source_reference = $("input[name='ecocash_source_reference']").val();
                // var ecocash_Merchant_MSISDN = $("input[name='ecocash_Merchant_MSISDN']").val();
                var totalAmount = $("input[name='total-amount']").val();
                totalAmount = parseFloat(totalAmount).toFixed(2);


                var jsonData = {
                    "mobile_number": ecocash_mobile,
                    "total_amount": totalAmount,
                };

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
                console.log(jsonData);

                //    ECOCASH request
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });


                $.ajax({
                    url: "{{ route('register.ecoCashResponse') }}",
                    method: "POST",
                    cache: false,
                    data: jsonData,
                    success: function(response) {
                        // ecocash response
                        var data = response;
                        console.log(data);
                        if ($.isEmptyObject(data.errors)) {
                            if (data.error) {
                                $(".payement-error").find("ul").html('');
                                $(".payement-error").css('display', 'block');
                                $(".payement-error").find("ul").append('<li>' + data.error + '</li>');
                            } else {
                                var response = JSON.parse(data.success);
                                if (response.return.field1 == 200) {
                                    $(".payement-error").find("ul").html('');
                                    $(".payement-error").css('display', 'block');
                                    $(".payement-error").find("ul").append('<li>' + data.success.return+'</li>');
                                } else {
                                    $(".payement-error").find("ul").html('');
                                    $(".payement-error").css('display', 'block');
                                    $(".payement-error").find("ul").append('<li>' + response.return.field2 +
                                        '</li>');
                                }


                            }
                        } else {
                            printErrorMsg(data.errors);
                        }


                    }
                });
            }



            /*****  Show Candidate  *******/

            showCandidateInformation() ;
            function  showCandidateInformation() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var i = 0;
                $.ajax({
                    url:"{{ route('candidate.profile.show')}}",
                    method: "GET",
                    success: function(data) {
                        var parent = "#msform";
                        var candidate = data.candidate === null ? {} : data.candidate;
                        var guardian = data.guardian === null ? {} : data.guardian;
                        var paid_fee = data.paid_fee === null ? {} : data.paid_fee;
                        var special_need = data.specialNeed === null ? {} : data.specialNeed;
                    $(`form${parent} #candidate-information input,form${parent} #candidate-information select, form${parent} #candidate-information textarea`)
                            .each(
                                function(index) {
                                    var input = $(this);
                                    var type = input.prop('type');
                                    var candidate_prifix_length= "candidate_".length;
                                    var name = input.attr('name').slice(candidate_prifix_length);


                                    if (type!="hidden") {
                                        $(`form${parent} #candidate-information [name='candidate_${name}']`)
                                            .val(candidate.hasOwnProperty(name) ? candidate[name] : '')
                                    }




                                }
                            );

                            $(`form${parent} #candidate-information [name='candidate_${name}']`)
                                            .val(candidate.hasOwnProperty(name) ? candidate[name] : '')

                      // specialNeed
                      $(`form${parent} #candidate-information [name='special_need']`)
                                            .val(special_need.hasOwnProperty('arrangement_id') ? special_need.arrangement_id : '')
                            //guardian
                        $(`form${parent} #candidate-guardian input,form${parent} #candidate-guardian select, form${parent} #candidate-guardian textarea`)
                            .each(
                                function(index) {
                                    var input = $(this);
                                    var type = input.prop('type');
                                    var guardian_prifix_length = "guardian_".length;
                                    var original_name=input.attr('name');
                                    var name = input.attr('name').slice(guardian_prifix_length);
                                    if (type!="hidden") {
                                        if (original_name=="guardian_type") {
                                            $(`form${parent} #candidate-guardian [name='${original_name}']`)
                                            .val(guardian.hasOwnProperty(original_name) ? guardian[original_name] : '')
                                        }else{
                                            $(`form${parent} #candidate-guardian [name='guardian_${name}']`)
                                            .val(guardian.hasOwnProperty(name) ? guardian[name] : '')
                                        }

                                    }






                                }
                            );




                    }
                });

            }

            /*****Show Update Candidate Endr *******/

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
                    url: "{{ route('candidate.transaction') }}",
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
                                $('.timetable-btns').show();
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
                var totalAmount = $("input[name='total-amount']").val();
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

                            printErrorMsg(data.errors);
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
                            printErrorMsg(data.errors);
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
                $.each(errors, function(key, errors) {
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
    @endpush
@endsection
