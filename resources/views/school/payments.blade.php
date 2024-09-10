@extends('layouts.school')
<script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.min.js"></script>
<script src="https://portal.nedsecure.co.za/scripts/bootstrap/js/bootstrap.min.js"></script>
<script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>


<script>
    liteboxInitialise('https://portal.nedsecure.co.za', "msform");
</script>

@section('content')
    <div id="page-wrapper">

        <!-- Current page dir -->
        <div class="header">
            <h1 class="page-header">
                Payments

            </h1>

            <ol class="breadcrumb">
                <li><a href="javascript:void();">Home</a></li>
                <li class="active"><a href="javascript:void();"> Payments</a></li>
            </ol>

        </div>
        <!-- end Current page dir -->

        <!-- Page content section -->
        <div id="page-inner">
            <!-- Candidate registration template section -->
            <div class="row">
                <div class="col-md-12">

                    <!-- Panel -->
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <div class="tabbable-panel">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#make-payments" data-toggle="tab">Sponsor payment
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#payments-history" data-toggle="tab">Payments history</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="make-payments">
                                            <!-- MultiStep Form -->
                                            <section id="grad1">
                                                <div class="container">
                                                    <div class="row justify-content-center">

                                                        <div class="col-md-12 text-center">
                                                            <div class="card">

                                                                <!-- <p>Fill all form field to go to next step</p> -->
                                                                <div class="row justify-content-center">
                                                                    <div class="col-md-12">
                                                                        <form action="" method="post" id="msform">
                                                                            <!-- progressbar -->
                                                                            <div class="progressbar">

                                                                                <ul id="progressbar_content">
                                                                                    <li class="active" id="invoice">
                                                                                        <strong><i
                                                                                                class="fas fa-receipt"></i>Bill</strong>
                                                                                    </li>
                                                                                    <li id="payment"><strong><i
                                                                                                class="far fa-credit-card"></i>Payment</strong>
                                                                                    </li>
                                                                                    <li id="confirm"><strong><i
                                                                                                class="fas fa-check"></i>Finish</strong>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>

                                                                            <fieldset>
                                                                                <div class="fieldset_container">
                                                                                    <div class="form-card">
                                                                                        <div class="invoice-wrapper">
                                                                                            <div class="invoice-top">
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-7">
                                                                                                        <div
                                                                                                            class="invoice-top-left">
                                                                                                            <h2
                                                                                                                class="client-company-name">
                                                                                                                {{ auth()->user()->center_name }}
                                                                                                                -
                                                                                                                {{ auth()->user()->center_no }}
                                                                                                            </h2>
                                                                                                            <h4
                                                                                                                class="terms">
                                                                                                                Note: <i>
                                                                                                                    Examination
                                                                                                                    registration
                                                                                                                    for the
                                                                                                                    year
                                                                                                                    {{ date('Y') }}.</i>

                                                                                                            </h4>
                                                                                                            <div
                                                                                                                class="invoice-bottom-right">


                                                                                                                <ul>
                                                                                                                    <li
                                                                                                                        class="text-danger">
                                                                                                                        Select
                                                                                                                        candidate
                                                                                                                        from
                                                                                                                        the
                                                                                                                        dropdown.
                                                                                                                    </li>
                                                                                                                    <li
                                                                                                                        class="text-danger">
                                                                                                                        click
                                                                                                                        "+
                                                                                                                        Add
                                                                                                                        Button"
                                                                                                                        to
                                                                                                                        add
                                                                                                                        another
                                                                                                                        candidate

                                                                                                                    </li>
                                                                                                                    <li
                                                                                                                        class="text-danger">
                                                                                                                        Deadline
                                                                                                                        for
                                                                                                                        payments:

                                                                                                                        @switch($center->level)
                                                                                                                            @case('G7ELT')
                                                                                                                                30th
                                                                                                                                May
                                                                                                                                {{ date('Y') }}
                                                                                                                            @break

                                                                                                                            @case('LGCSE')
                                                                                                                                29th
                                                                                                                                April
                                                                                                                                {{ date('Y') }}
                                                                                                                            @break

                                                                                                                            @default
                                                                                                                        @endswitch

                                                                                                                    </li>

                                                                                                                    <li
                                                                                                                        class="text-danger">
                                                                                                                        Proof
                                                                                                                        of
                                                                                                                        payment
                                                                                                                        will
                                                                                                                        be
                                                                                                                        send
                                                                                                                        to:
                                                                                                                        <b>
                                                                                                                            {{ auth()->user()->email }}</b>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-sm-5">
                                                                                                        <div
                                                                                                            class="invoice-top-right">

                                                                                                            <ul
                                                                                                                class="list-group">
                                                                                                                <li
                                                                                                                    class="list-group-item">
                                                                                                                    Expected
                                                                                                                    Payment
                                                                                                                    <span
                                                                                                                        class="badge">LSL
                                                                                                                        <span
                                                                                                                            class="expectedFee"></span></span>
                                                                                                                </li>
                                                                                                                <li
                                                                                                                    class="list-group-item">
                                                                                                                    Paid
                                                                                                                    Amount<span
                                                                                                                        class="badge">LSL
                                                                                                                        <span
                                                                                                                            class="paidFee"></span>
                                                                                                                    </span>
                                                                                                                </li>
                                                                                                                <li
                                                                                                                    class="list-group-item">
                                                                                                                    Expected
                                                                                                                    Balance
                                                                                                                    <span
                                                                                                                        class="badge">LSL
                                                                                                                        <span
                                                                                                                            class="balanceFee"></span></span>
                                                                                                                </li>
                                                                                                            </ul>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="invoice-bottom">
                                                                                                <div class="row">
                                                                                                    <div class="clearfix">
                                                                                                    </div>

                                                                                                    <div
                                                                                                        class="col-sm-12 col-md-12">
                                                                                                        <div
                                                                                                            class="invoice-bottom-left">
                                                                                                            <div
                                                                                                                class="row">
                                                                                                                <div
                                                                                                                    class="form-group col-sm-12 col-md-6">
                                                                                                                    <label
                                                                                                                        for="amount">*Amount
                                                                                                                        to
                                                                                                                        pay</label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        class="form-control"
                                                                                                                        readonly
                                                                                                                        name="amount"
                                                                                                                        id="amount"
                                                                                                                        placeholder="Enter Amount eg (2000)">

                                                                                                                    <input
                                                                                                                        type="hidden"
                                                                                                                        name="grand_total"
                                                                                                                        id="grand_total"
                                                                                                                        value="">


                                                                                                                    <input
                                                                                                                        type="hidden"
                                                                                                                        name="candidate_profile"
                                                                                                                        id="candidate_profile"
                                                                                                                        value="">
                                                                                                                    <input
                                                                                                                        type="hidden"
                                                                                                                        name="guardian_profile"
                                                                                                                        id="guardian_profile"
                                                                                                                        value="">



                                                                                                                    <input
                                                                                                                        type="hidden"
                                                                                                                        name="email_address"
                                                                                                                        id="email_address"
                                                                                                                        readonly
                                                                                                                        value="{{ auth()->user()->email }}"
                                                                                                                        placeholder="Enter  Email">
                                                                                                                </div>

                                                                                                            </div>

                                                                                                            <div
                                                                                                                class="pull-right candidate-payment-check">
                                                                                                                <div
                                                                                                                    class="[ form-group ]">
                                                                                                                    <input
                                                                                                                        type="checkbox"
                                                                                                                        name="all_candidates"
                                                                                                                        value="1"
                                                                                                                        id="fancy-checkbox-primary"
                                                                                                                        autocomplete="off" />
                                                                                                                    <div
                                                                                                                        class="[ btn-group ]">
                                                                                                                        <label
                                                                                                                            for="fancy-checkbox-primary"
                                                                                                                            class="[ btn btn-primary ]">
                                                                                                                            <span
                                                                                                                                class="[ glyphicon glyphicon-ok ]"></span>
                                                                                                                            <span> </span>
                                                                                                                        </label>
                                                                                                                        <label
                                                                                                                            for="fancy-checkbox-primary"
                                                                                                                            class="[ btn btn-primary active ]">
                                                                                                                            <i class="fa fa-reply-all"
                                                                                                                                aria-hidden="true"></i>
                                                                                                                            All
                                                                                                                            Candidate
                                                                                                                        </label>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <table
                                                                                                                id="dynamic_candidate"
                                                                                                                class="table">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th>Candidate
                                                                                                                            Names
                                                                                                                        </th>
                                                                                                                        <th>Candidate
                                                                                                                            No
                                                                                                                        </th>
                                                                                                                        <th>Exam
                                                                                                                            Fee
                                                                                                                        </th>
                                                                                                                        <th>Action
                                                                                                                        </th>
                                                                                                                    </tr>
                                                                                                                </thead>

                                                                                                                <tbody>

                                                                                                                </tbody>
                                                                                                            </table>





                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="col-md-7 col-sm-7">

                                                                                                    </div>
                                                                                                    <div class="clearfix">
                                                                                                    </div>
                                                                                                    <div class="col-xs-12">
                                                                                                        <hr
                                                                                                            class="divider">
                                                                                                    </div>
                                                                                                    <div class="col-sm-3">
                                                                                                        <h6
                                                                                                            class="text-left">
                                                                                                            @switch($center->level)
                                                                                                                @case('G7ELT')
                                                                                                                    g7support@ecol.org.ls
                                                                                                                @break

                                                                                                                @case('LGCSE')
                                                                                                                    support@ecol.org.ls
                                                                                                                @break

                                                                                                                @default
                                                                                                            @endswitch

                                                                                                        </h6>
                                                                                                    </div>
                                                                                                    <div class="col-sm-3">
                                                                                                        <h6
                                                                                                            class="text-center">
                                                                                                            +266 5230 0132
                                                                                                        </h6>
                                                                                                    </div>
                                                                                                    <div class="col-sm-3">
                                                                                                        <h6
                                                                                                            class="text-right">
                                                                                                            +266
                                                                                                            2231 2880</h6>
                                                                                                    </div>
                                                                                                    <div class="col-sm-3">
                                                                                                        <h6
                                                                                                            class="text-right">
                                                                                                            +266
                                                                                                            52300111</h6>
                                                                                                    </div>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    @permission('payments-make')
                                                                                        <input type="submit" name="next"
                                                                                            class="next action-button"
                                                                                            value="Next">
                                                                                    @endpermission
                                                                                </div>
                                                                            </fieldset>

                                                                            <!--Payment  -->
                                                                            <fieldset>

                                                                                <div class="fieldset_container payment">

                                                                                    <div class="form-card">
                                                                                        <p class="text-danger">Please
                                                                                            select any
                                                                                            Method of payment below</p>
                                                                                        <p class="payement-error"></p>
                                                                                        <ul class="nav nav-pills">
                                                                                            <li>

                                                                                                <a href="#home-pills"
                                                                                                    data-toggle="tab">
                                                                                                    Electronic Funds
                                                                                                    Transfer (EFT)
                                                                                                    <input type="radio"
                                                                                                        name="payment"
                                                                                                        checked
                                                                                                        value="CreditCard" />
                                                                                                </a>
                                                                                            </li>
                                                                                            {{-- <li>

                                                                                                <a href="#profile-pills"
                                                                                                    data-toggle="tab">
                                                                                                    Upload Proof of Payment
                                                                                                    <input type="radio"
                                                                                                        name="payment"
                                                                                                        value="CashDeposit" />
                                                                                                </a>

                                                                                            </li> --}}
                                                                                            <li>


                                                                                                <a href="#profile-mpesa"
                                                                                                    data-toggle="tab">
                                                                                                    M-PESA
                                                                                                    <input type="radio"
                                                                                                        name="payment"
                                                                                                        value="VclMpesa" />
                                                                                                </a>

                                                                                            </li>

                                                                                        </ul>

                                                                                        <div class="tab-content">
                                                                                            <div class="tab-pane fade in"
                                                                                                id="home-pills">
                                                                                                <div class="form-group">
                                                                                                    <h2>electronic funds
                                                                                                        transfer
                                                                                                        (EFT)</h2>
                                                                                                    <p>Welcome to ECoL
                                                                                                        secure online
                                                                                                        payment. Proof of
                                                                                                        payment will be send
                                                                                                        to your
                                                                                                        email on successful
                                                                                                        payment</p>
                                                                                                    <p>Don't hesitate to
                                                                                                        contact us
                                                                                                        for any concerns!
                                                                                                    </p>
                                                                                                    <a
                                                                                                        id="iveri-litebox-button">Pay
                                                                                                        now</a>
                                                                                                    <!-- Iveri start-->
                                                                                                    <input type="hidden"
                                                                                                        value="{{ auth()->user()->center_name }}"
                                                                                                        readonly="readonly"
                                                                                                        name="Ecom_BillTo_Postal_Name_Prefix"
                                                                                                        id="Ecom_BillTo_Postal_Name_Prefix"
                                                                                                        class="clsInputReadOnlyText" />
                                                                                                    <input
                                                                                                        name="Ecom_BillTo_Postal_Name_First"
                                                                                                        readonly="readonly"
                                                                                                        type="hidden"
                                                                                                        value="{{ auth()->user()->center_name }}"
                                                                                                        id="Ecom_BillTo_Postal_Name_First"
                                                                                                        class="clsInputReadOnlyText" />
                                                                                                    <input type="hidden"
                                                                                                        name="Ecom_BillTo_Postal_Name_Middle"
                                                                                                        id="Ecom_BillTo_Postal_Name_Middle" />

                                                                                                    <input
                                                                                                        name="Ecom_BillTo_Postal_Name_Last"
                                                                                                        readonly="readonly"
                                                                                                        type="hidden"
                                                                                                        value="{{ auth()->user()->center_name }}"
                                                                                                        style="width: 20px;"
                                                                                                        id="Ecom_BillTo_Postal_Name_Last"
                                                                                                        class="clsInputReadOnlyText" />
                                                                                                    <input
                                                                                                        name="Ecom_BillTo_Online_Email"
                                                                                                        readonly="readonly"
                                                                                                        type="hidden"
                                                                                                        value="jdoe@mail.com"
                                                                                                        maxlength="50"
                                                                                                        id="Ecom_BillTo_Online_Email"
                                                                                                        class="clsInputReadOnlyText" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Ecom_ShipTo_Postal_Street_Line1"
                                                                                                        id="Ecom_ShipTo_Postal_Street_Line1"
                                                                                                        value="50 Sunny Drive Avenue" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Ecom_ShipTo_Postal_Street_Line2"
                                                                                                        id="Ecom_ShipTo_Postal_Street_Line2"
                                                                                                        value="Sunsetville" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Ecom_ShipTo_Postal_City"
                                                                                                        id="Ecom_ShipTo_Postal_City"
                                                                                                        value="Johannesburg" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Ecom_ShipTo_Postal_StateProv"
                                                                                                        id="Ecom_ShipTo_Postal_StateProv"
                                                                                                        value="Gauteng" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Ecom_ShipTo_Postal_PostalCode"
                                                                                                        id="Ecom_ShipTo_Postal_PostalCode"
                                                                                                        value="2185" />

                                                                                                    <input
                                                                                                        name="Ecom_ConsumerOrderID"
                                                                                                        readonly="readonly"
                                                                                                        type="hidden"
                                                                                                        value="AUTOGENERATE"
                                                                                                        maxlength="20"
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
                                                                                                        id="Lite_Authorisation"
                                                                                                        value="false" />
                                                                                                    <input type="hidden"
                                                                                                        name="Lite_Version"
                                                                                                        id="Lite_Version"
                                                                                                        value="2.0" />
                                                                                                    <!-- Ecml end-->

                                                                                                    <!-- Lite_Order_LineItems_Product -->
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Lite_Order_LineItems_Product_1"
                                                                                                        id="Lite_Order_LineItems_Product_1"
                                                                                                        value="Total Subjects" />
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        name="Lite_Order_LineItems_Quantity_1"
                                                                                                        id="Lite_Order_LineItems_Quantity_1"
                                                                                                        value="1" />

                                                                                                    <!-- Lite_Order_LineItems_Amount -->
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
                                                                                                        class="clsInputReadOnlyText"
                                                                                                        name="Transaction_LineItems_Amount_1"
                                                                                                        id="Transaction_LineItems_Amount_1"
                                                                                                        value="100" />
                                                                                                    <input type="hidden"
                                                                                                        name="Lite_Order_LineItems_Amount_1"
                                                                                                        id="Lite_Order_LineItems_Amount_1"
                                                                                                        value="100" />


                                                                                                    <!-- Transaction Amount -->
                                                                                                    <input
                                                                                                        name="Transaction_Amount"
                                                                                                        id="Transaction_Amount"
                                                                                                        type="hidden"
                                                                                                        value="100"
                                                                                                        class="clsInputText"
                                                                                                        value="100" />
                                                                                                    <input type="hidden"
                                                                                                        value="100"
                                                                                                        name="Lite_Order_Amount"
                                                                                                        id="Lite_Order_Amount" />


                                                                                                    <!-- Merchant_Application ID -->
                                                                                                    <input
                                                                                                        name="Merchant_ApplicationID"
                                                                                                        type="hidden"
                                                                                                        value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}"
                                                                                                        maxlength="40"
                                                                                                        id="Merchant_ApplicationID"
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
                                                                                                    <input type="hidden"
                                                                                                        readonly="readonly"
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
                                                                                            <div class="tab-pane fade"
                                                                                                id="profile-pills">
                                                                                                <h2>Upload Proof of Payment
                                                                                                </h2>
                                                                                                <p>Welcome to ECoL secure
                                                                                                    online
                                                                                                    payment. Proof of
                                                                                                    payment will be send to
                                                                                                    your
                                                                                                    email on successful
                                                                                                    payment</p>
                                                                                                <p>Don't hesitate to contact
                                                                                                    us for
                                                                                                    any concerns!
                                                                                                </p>

                                                                                                <div class="mt-2">
                                                                                                    <button type="button"
                                                                                                        class="btn btn-primary btn-bank-statement"
                                                                                                        data-toggle="modal"
                                                                                                        data-target="#bank-statement">
                                                                                                        Upload proof of
                                                                                                        payment
                                                                                                    </button>

                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="tab-pane fade"
                                                                                                id="profile-mpesa">
                                                                                                <h2>M-PESA</h2>
                                                                                                <p>Welcome to ECoL secure
                                                                                                    online payment. Proof of
                                                                                                    payment
                                                                                                    will be send to your
                                                                                                    mobile phone on
                                                                                                    successful payment
                                                                                                </p>
                                                                                                <p>Don't hesitate to contact
                                                                                                    us for any concerns!</p>
                                                                                                <div class="form-group">
                                                                                                    <label
                                                                                                        for="mpesa_mobile">Phone
                                                                                                        number</label>
                                                                                                    <input type="text"
                                                                                                        name="mpesa_mobile"
                                                                                                        class="form-control"
                                                                                                        id="mpesa_mobile"
                                                                                                        placeholder="*Phone number">

                                                                                                </div>
                                                                                                <a href="javascript:void(0)"
                                                                                                    id="mpesa_pay"
                                                                                                    class="btn btn-primary">Pay
                                                                                                    now </a>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="button" name="previous"
                                                                                        class="previous action-button-previous"
                                                                                        value="Previous">

                                                                                    <input type="submit"
                                                                                        name="make_payment"
                                                                                        class="next action-button" disabled
                                                                                        value="Next">
                                                                                </div>

                                                                            </fieldset>
                                                                            <!-- Finish -->
                                                                            <fieldset>
                                                                                <div class="fieldset_container">
                                                                                    <div class="form-card">
                                                                                        <div
                                                                                            class="row justify-content-center">


                                                                                            <div
                                                                                                class="modalbox success col-sm-12 col-md-8 col-lg-6 center animate">
                                                                                                <div class="icon">
                                                                                                    <span
                                                                                                        class="glyphicon glyphicon-ok"></span>
                                                                                                </div>
                                                                                                <!--/.icon-->
                                                                                                <h1>Success!</h1>
                                                                                                <p>We will sent the payment
                                                                                                    confirmation to<br />
                                                                                                    your
                                                                                                    e-mail!
                                                                                                </p>

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
                                        <div class="tab-pane " id="payments-history">
                                            <div class="p-0 mt-3 mb-2">
                                                <table class="table display compact" id="paymets-history-datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Uploaded Date</th>
                                                            <th>Center no</th>
                                                            <th>Email</th>
                                                            <th>Amount paid</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                                @push('scripts')
                                                    <script>
                                                        $(function() {

                                                            /****  Payement  statement Start *******/
                                                            paymentStatement();
                                                            function paymentStatement() {
                                                                $.ajaxSetup({
                                                                    headers: {
                                                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                                                    },
                                                                });
                                                                $.ajax({
                                                                    url: "/center/payment-statement",
                                                                    type: 'GET',
                                                                    dataType: 'json', // added data type
                                                                    success: function(data) {
                                                                        console.log(data)
                                                                        var sponsors = data.sponsors;
                                                                        var total_candidates = data.total_candidates;
                                                                        var total_amount = data.total_overdue
                                                                        var balance = data.balance
                                                                        var total_paid=data.total_paid;
                                                                        $(".expectedFee").html(total_amount.toFixed(2));
                                                                        $(".balanceFee").html(parseFloat(balance).toFixed(2));
                                                                        $(".paidFee").html(parseFloat(total_paid).toFixed(2));
                                                                    }
                                                                });

                                                            }
                                                            /****  Payement  statement End *******/
                                                            var table = $('#paymets-history-datatable').DataTable({
                                                                processing: true,
                                                                serverSide: true,
                                                                responsive: true,
                                                                deferRender: true,
                                                                "lengthMenu": [
                                                                    [10, 50, 100, 200, 1000, -1],
                                                                    [10, 50, 100, 200, 1000, "All"]
                                                                ],
                                                                ajax: {
                                                                    url: "{{ route('center.payments.index') }}"
                                                                },

                                                                columns: [{
                                                                        data: 'updated_at',
                                                                        name: 'updated_at'
                                                                    },
                                                                    {
                                                                        data: 'center_id',
                                                                        name: 'center_id'
                                                                    },
                                                                    {
                                                                        data: 'email',
                                                                        name: 'email'

                                                                    },
                                                                    {
                                                                        data: 'amount_paid',
                                                                        name: 'amount_paid'

                                                                    },
                                                                    {
                                                                        data: 'checked_status',
                                                                        name: 'checked_status'
                                                                    },

                                                                ]
                                                            });
                                                            $("#paymets-history-datatable").css("width", "100%");
                                                        });
                                                        /*****  Display candidates end*******/
                                                    </script>
                                                @endpush
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End Panel -->




                </div>
            </div>
            <!-- end Candidate registration template section -->
        </div>
        <!-- end Page content section -->

    </div>
    <!-- /. PAGE WRAPPER  -->
    <!-- Modal HTML -->
    <div id="iveri-litebox" class="center-block"></div>

    <!--Bank stament Modal -->
    <div class="modal fade " id="bank-statement" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"> Upload Bank Statement</h5>
                </div>
                <div class="modal-body">
                    <form action="" id="upload-bank-form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="files">
                            <ul></ul>
                        </div>
                        <div>
                            <label for="upload">
                                <input type="file" name="bank_statement" id="upload">
                                Upload File
                            </label>
                        </div>
                    </form>

                </div>

                <div class="modal-footer">

                    <button type="submit" name="upload-bank-statement" class="btn btn-primary"
                        id="upload-bank">Upload</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
    <!--End Bank stament Modal -->
@endsection

@section('script')
    <script>
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
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }


        $(document).ready(function() {



            $("[data-toggle='tab']").click(function() {

                $('.nav-pills').find('input').removeAttr('checked');
                $(event.target).find('input').attr('checked', 'checked');
            });

            /*****  Dynamic Table with*******/

            var candidateState = {};
            var added = 0;



            var count = 1;

            dynamic_field(count);


            $(document).on('click', '#add-candidate', function() {
                count++;
                dynamic_field(count);
            });

            $(document).on('click', '.remove-candidate', function() {
                count--;
                $(this).closest("tr").remove();
                addToCart();
            });

            $(document).on('change', '.candidate_name', function() {
                var selected_candidate = $(this).find(":selected").val();
                if (selected_candidate != "") {
                    //Create array of input values
                    var candidate_numbers = $('input[name^="candidate_no"]').map(function() {
                        if ($(this).val() != '') return $(this).val()
                    }).get();

                    //show/hide error msg
                    if (($.inArray(selected_candidate, candidate_numbers) > -1)) {
                        toastr.error("candidate already selected " + selected_candidate);
                        $(this).val("").change();
                    } else {
                        const found_names = candidateState.candidates.filter(v => v.candidate_no == $(this)
                            .val());
                        if (found_names[0].candidate_profile == "") {
                            toastr.error(
                                "please sign in  at school candidate and complete the candidate profile " +
                                selected_candidate);
                        } else if (found_names[0].guardian_profile == "") {
                            toastr.error(
                                "please sign in  at school candidate and complete the  Next of Kin profile " +
                                selected_candidate);
                        } else {
                            $(this).closest("tr").find(".candidate_no").val(found_names[0].candidate_no);
                            $(this).closest("tr").find(".exam_fee").val(found_names[0].exam_fee);
                            addToCart();
                        }

                    }
                } else {
                    $(this).closest("tr").find(".candidate_no").val("");
                    $(this).closest("tr").find(".exam_fee").val("");
                    addToCart();
                }

                //(unique.length != 0) ? $('.error').text('duplicate'): $('.error').text('');
            });

            $(document).on('change', "[name='all_candidates']", function(ev) {
                var grand_total = $("#grand_total").val();
                var candidate_profile = $("#candidate_profile").val();
                var guardian_profile = $("#guardian_profile").val();


                if ($(this).prop('checked') == true && grand_total != "" && guardian_profile == "0" &&
                    candidate_profile == "0") {
                    $(this).attr("checked", true)
                    $('.remove-candidate').each(function(i, obj) {
                        count--;
                        $(this).closest("tr").remove();
                        addToCart();
                    });
                    $(".candidate_name").val("").change();
                    $("#amount").val(grand_total);
                } else {
                    $(this).removeAttr('checked');
                    $("#amount").val('');
                    toastr.error("Please check candidates profile and and next of kin");
                }


            });


            $(document).on("click", "#mpesa_pay", function() {
                vclMpesaComplete($(this));
            });


            // btn-bank-statement
            $(document).on("click", "#bank-statement #upload-bank", function(e) {

                e.preventDefault();
                var formData = new FormData();


                $('input[name="candidate_no[]"]').each(function() {
                    if ($(this).val() != "") {
                        formData.append("candidate_no[]", $(this).val());
                    }
                });
                var email_Address = $("input[name='email_address']").val();
                var payment = $("input[name='payment']:checked").val();
                var all_candidate = $("input[name='all_candidates']:checked").val();
                var totalfiles = state.filesArr.length;
                formData.append("email", email_Address);
                formData.append("payment", payment);
                formData.append("all_candidates", typeof all_candidate === "undefined" ? null :
                    all_candidate);
                for (var index = 0; index < totalfiles; index++) {
                    formData.append("bank_statement[]", state.filesArr[index]);
                }
                var caption = $(this).html();

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                $.ajax({
                    url: "{{ route('center.payments.makepayment') }}",
                    method: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data) {

                        if ($.isEmptyObject(data.error)) {
                            $("#bank-statement ul").html("");
                            $("#upload-bank-form").trigger("reset");
                            $("#bank-statement").modal("hide");
                            $('input[name="make_payment"]').prop("disabled", false);
                            $('input[name="make_payment"]').trigger("click");
                            toastr.success(data.success);
                        } else {
                            printErrorMsg(data.error);
                        }

                    },

                });
            });


            function dynamic_field(number) {

                //Create array of input values

                var candidate_number = $('input[name^="candidate_no"]').map(function() {
                    return this.value;
                }).get();


                var duplicateCandidate_number = candidate_number.filter(function(item, index) {
                    return candidate_number.indexOf(item) != index;
                });
                if (duplicateCandidate_number.length > 0) {
                    toastr.error("Duplicate Candidate numbers found: " + duplicateCandidate_number.join(", "));

                    $('input[name^="candidate_no"]').each(function() {
                        if (duplicateCandidate_number.indexOf(this.value) != -1) {
                            $(this).closest('tr').css("background-color", "red");
                        }
                    });
                } else {
                    html = '<tr>';
                    html +=
                        `<td><select name="full_names[]" class="form-control candidate_name" id="candidate-${number}"></select></td>`;
                    html +=
                        '<td><input type="text" name="candidate_no[]"  class="form-control candidate_no" readonly/></td>';
                    html +=
                        '<td><input type="text" name="exam_fee[]" class="form-control exam_fee" readonly /></td>';
                    if (number > 1) {
                        html +=
                            '<td><button type="button" name="remove" id="remove" class="btn btn-sm btn-danger remove-candidate">-</button></td></tr>';
                        $("#dynamic_candidate tbody").append(html);
                    } else {
                        html +=
                            '<td><button type="button" name="add-candidate" id="add-candidate" class="btn btn-sm btn-success">+</button></td></tr>';
                        $("#dynamic_candidate tbody").html(html);

                    }
                    getCandidateList(number);

                }

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
                    url: "{{ route('center.payments.makepayment') }}",
                    method: "POST",
                    data: inputData,
                    beforeSend: function() {
                        element.prop('disabled', true).html("Processing....");

                    },
                }).done(function(data) {
                    var data = isJsonString(data) ? $.parseJSON(data) : data;

                    element.prop('disabled', false).html(caption);
                    if ($.isEmptyObject(data.errors)) {
                        // var data = $.parseJSON(data);
                        element.prop('disabled', false).html(caption);
                        if (data.status == 1) {
                            $('input[name="make_payment"]').prop("disabled", false);
                            $('input[name="make_payment"]').trigger("click");
                        }
                    } else {
                        printErrorMsg('#msform', data.errors)
                        element.prop('disabled', false).html(caption);
                    }

                }).fail(function(xhr, status, error) {
                    element.prop('disabled', false).html(caption);
                });
            }

            function isJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }

            function addToCart() {
                //init
                var total_fee = 0;
                $("[name='exam_fee[]']").each(function() {
                    if ($(this).val() != "") {
                        total_fee = total_fee + parseFloat($(this).val());
                    }

                });
                if (total_fee != 0) {
                    $("#amount").val(total_fee);
                } else {
                    $("#amount").val(total_fee);
                    // $("#amount").removeAttr("value");
                }




            }



            // state management
            function updateCandidateState(newState) {
                candidateState = {
                    ...newState
                };

            }

            function getCandidateList(number) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('center.payment.candidates') }}",
                    method: "GET",
                    cache: false,
                    success: function(data) {

                        var candidates = data.candidates;
                        var grand_total = data.grand_total;
                        var candidate_profile = data.candidate_profile;
                        var guardian_profile = data.guardian_profile;
                        $("#grand_total").val(grand_total);
                        $("#candidate_profile").val(candidate_profile);
                        $("#guardian_profile").val(guardian_profile);
                        if (added == 0) {
                            updateCandidateState({
                                candidates,
                            });
                            added++;
                        }
                        $(`#candidate-${number}`).empty().append($('<option>', {
                            value: "",
                            text: "Please Select candidate"
                        }));
                        $.each(candidates, function(i, item) {
                            $(`#candidate-${number}`).append($('<option>', {
                                value: item.candidate_no,
                                text: `${item.candidate_no} - ${item.national_id}  ${item.candidate_surname} ${item.candidate_other_name}`
                            }));
                        });
                    }
                });
            }




        });






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
                url: "{{ route('center.payments.makepayment') }}",
                method: "POST",
                cache: false,
                data: inputData,
                success: function(data) {

                    if (data.status == 1) {
                        $(".modal-backdrop").hide();
                        $('input[name="make_payment"]').prop("disabled", false);
                        $('input[name="make_payment"]').trigger("click");
                        $("#iveri-litebox").removeClass('show');
                        $(".modal-backdrop").css({
                            zIndex: "-1"
                        });

                    } else {

                        $("#iveri-litebox").removeClass('show');
                        $(".modal-backdrop").hide();
                        $(".modal-backdrop").css({
                            zIndex: "-1"
                        });

                        $('.payement-error').show();
                        $('.payement-error').html(
                            "Dear valued customer, your transaction has declined! Please try again,<a href='payments.php'>Reregister</a> "
                        );
                        $('#iveri-litebox-button').hide();

                    }

                }
            });
        }









        // // no react or anything
        var state = {};

        // state management
        function updateState(newState) {
            state = {
                ...newState
            };

        }

        // // event handlers
        $("#bank-statement #upload").change(function(e) {
            var files = $(this)[0].files;


            if (files.length > 0) {
                let filesArr = Array.from(files);

                updateState({
                    files: files,
                    filesArr: filesArr
                });
                $('#bank-statement div.files').css({
                    'display': 'block'
                })
                renderFileList();

            }

        });

        $("#bank-statement .files").on("click", "li > i", function(e) {
            let key = $(this).
            parent().
            attr("key");
            let curArr = state.filesArr;
            curArr.splice(key, 1);
            updateState({
                filesArr: curArr
            });
            renderFileList();
        });





        // errors

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

        function isNumeric(str) {
            if (typeof str != "string") return false // we only process strings!
            return !isNaN(str) &&
                // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
                !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
        }
        // render functions
        function renderFileList() {
            let fileMap = state.filesArr.map((file, index) => {
                let suffix = "bytes";
                let size = file.size;
                if (size >= 1024 && size < 1024000) {
                    suffix = "KB";
                    size = Math.round(size / 1024 * 100) / 100;
                } else if (size >= 1024000) {
                    suffix = "MB";
                    size = Math.round(size / 1024000 * 100) / 100;
                }

                return `<li key="${index}">${file.name} <span class="file-size">${size} ${suffix}</span><i class="material-icons md-48">delete</i></li>`;
            });
            $("#bank-statement ul").html(fileMap);
        }
    </script>
@endsection
