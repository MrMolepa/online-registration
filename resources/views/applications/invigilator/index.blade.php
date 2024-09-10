<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invigilator Contract form | ECoL</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css'>
    <link rel="stylesheet" href="./style.css">
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:400,500,700,900&display=swap');

        body {
            background: #ecf0f4;
            width: 100%;
            height: 100%;
            font-size: 18px;
            line-height: 1.5;
            font-family: 'Roboto', sans-serif;
            color: #222;
        }

        .container {
            max-width: 1500px;
            width: 100%;
        }

        h1 {

            font-weight: 700;
            font-size: 45px;
            font-family: 'Roboto', sans-serif;
        }

        .header {
            margin-bottom: 40px;
        }

        #description {
            font-size: 24px;
        }

        .form-wrap {
            background: rgba(255, 255, 255, 1);
            width: 100%;
            max-width: 90%;
            padding: 50px;
            margin: 0 auto;
            position: relative;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
            -moz-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
            box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
        }

        .form-wrap:before {
            content: "";
            width: 90%;
            height: calc(100% + 60px);
            left: 0;
            right: 0;
            margin: 0 auto;
            position: absolute;
            top: -30px;
            background: #598abf;
            z-index: -1;
            opacity: 0.8;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
            -moz-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
            box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group>label {
            display: block;
            font-size: 14px;
            color: #000;
        }

        .custom-control-label {
            color: #000;
            font-size: 14px;
        }

        .form-control {

            background: #ecf0f4;
            border-color: transparent;
            padding: 0 5px;
            font-size: 16px;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #598abf;
            -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
        }

        textarea.form-control {
            height: 160px;
            padding-top: 15px;
            resize: none;
        }

        .btn {
            padding: .657rem .75rem;
            font-size: 18px;
            letter-spacing: 0.050em;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
            width: 150px;
        }

        .btn-primary {
            color: #fff;
            background-color: #598abf;
            border-color: #598abf;
        }

        .btn-primary:hover {
            color: #598abf;
            background-color: #ffffff;
            border-color: #598abf;
            -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
        }

        .btn-primary:focus,
        .btn-primary.focus {
            color: #598abf;
            background-color: #ffffff;
            border-color: #598abf;
            -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
        }

        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active,
        .show>.btn-primary.dropdown-toggle {
            color: #598abf;
            background-color: #ffffff;
            border-color: #598abf;
        }

        .btn-primary:not(:disabled):not(.disabled):active:focus,
        .btn-primary:not(:disabled):not(.disabled).active:focus,
        .show>.btn-primary.dropdown-toggle:focus {
            -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
            box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
        }


        /* Nav bar */
        .navbar-brand img {
            width: 15px;
        }

        .navbar {

            background-color: #598abf;
            margin-bottom: 10px;
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar .navbar-nav .nav-link {
            color: #fff;
            font-size: 1.1em;
            padding: 0.5em 1em;
        }

        @media screen and (min-width: 768px) {
            .navbar-brand img {
                width: 50px;
            }

            .navbar-brand {
                margin-right: 0;
                padding: 0 1em;
            }







        }
    </style>

</head>

<body>
    <!-- partial:index.partial.html -->
    <nav class="navbar navbar-expand-md navbar-dark nav-bar ">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Examination Council of Lesotho</a>
                    </li>

                    <a class="navbar-brand d-none d-md-block" href="#">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="img">
                    </a>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Handbook</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <header class="header">

            <h3 id="title" class="text-center">CONTRACT OF SERVICE</h3>
            <div id="description" class="text-center" value="{{ $invigilator->surname }}">
                between Examination Council of Lesotho and
                <div name="name" id="name" class="text-center">
                    {{ $invigilator->other_names }} {{ $invigilator->surname }}
                </div>
        </header>

        <div class="form-wrap">
            <form id="application-edit-form" method="POST" action="{{ $url }}">
                @csrf
                @method('PUT')
                <h5 class="text-center">----- PERSONAL INFORMATION -----</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">National ID</label>
                            <input readonly type="text" value="{{ $invigilator->national_id }}" name="national_id"
                                id="national_id" placeholder="ID number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Invigilator type</label>
                            <input readonly type="text"
                                value="{{ $invigilator->invigilation_role->invigilation_type->name }}"
                                name="invigilation_role_id" id="invigilation_role_id" placeholder="Enter your name"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Surname</label>
                            <input readonly type="text" value="{{ $invigilator->surname }}" name="surname"
                                id="surname" placeholder="Enter your name" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Other Names</label>
                            <input readonly type="text" value="{{ $invigilator->other_names }}" name="other_names"
                                id="other_names" placeholder="Enter your name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="text-label" for="text">Date of Birth</label>
                            <input type="date" value="{{ $invigilator->date_of_birth }}" name="date_of_birth"
                                id="date_of_birth" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Gender</label>
                            <select id="gender" name="gender" class="form-control">
                                <option selected value>Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Phone Number</label>
                            <input readonly type="text" value="{{ $invigilator->phone_number }}"
                                name="phone_number" id="phone_number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Email</label>
                            <input readonly type="email" value="{{ $invigilator->email }}" name="email"
                                id="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label id="name-label" for="name">Highest Qualification</label>
                            <input type="text" value="{{ $invigilator->qualification }}" name="qualification"
                                id="qualification" class="form-control">
                        </div>
                    </div>

                </div>
                <hr>
                <h5 class="text-center"> ----- METHOD OF PAYMENT -----</h5>
                <div class="row">
                    <div class="form group  ">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-0" role="tab">Payment
                                    rules</a>
                            </li>

                        </ul><!-- Tab panes -->
                        <br>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-0" role="tabpanel">
                                <p>You will be paid: <STRONG>M
                                        {{ $invigilator->invigilation_role->amount }}.00</STRONG>
                                    for
                                    temporary
                                    job position:
                                    <strong>{{ $invigilator->invigilation_role->invigilation_type->name }}</strong>
                                    <li>Please note that payments will be done on registered mobile number.</li>
                                    <li>The name that you have used to register your mobile/account number should be
                                        the
                                        same as the one on the contract.</li>
                                    <br>
                                <p><STRONG>NB:</STRONG> Choose your suitable payment method by clicking on payment
                                    method
                                    mentioned.</p>
                            </div>
                            <br>






                            <div class="form-group row col-sm-10">
                                <label for="invigilation_type" class="col-sm-4 col-form-label">Select your payment
                                    method</label>
                                <select class="form-control col-sm-6" id="payement-methods">
                                    <option selected>Select Payment Method</option>
                                    @foreach ($payment_methods as $payment_method)
                                        <option id="{{ $payment_method->id }}" name="{{ $payment_method->id }}"
                                            value="{{ $payment_method->id }}">{{ $payment_method->name }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>

                            <div id="payement-method-attributes">

                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h5 class="text-center"> ----- DECLARATION -----</h5>
                <div class="row">
                    <p> I declare that:
                        <li> I have no personal interest whatever in the examinations that I will be invigilating.
                        </li>
                        <li>I do not have any health problems that can affect my performance.</li>
                        <li>I have not been linked directly or indirectly to any malpractice in
                            examinations.
                        </li>
                        <br>
                    </p>
                    <div class="row col-sm-9">
                        <input type="checkbox" class="col-sm-1" name="terms" id="terms" />
                        <div class="col-sm-8"> I agree with terms and
                            conditions </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <button type="submit" id="submit-application" name="add-appliation"
                            class="btn btn-primary btn-block" disabled>Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- partial -->

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>


    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.min.js'></script>
    <script type="text/javascript">
        //update
        $(document).ready(function() {
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
                $.ajax({
                    type: "POST",
                    url: url,
                    data: editForm.serializeArray(),
                    success: function(data) {
                        console.log(data);

                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });


            //payement-methods

            $(document).on('change', '#payement-methods', function(e) {

                var id = $(this).val();


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "GET",
                    url: '{{ $geturl }}',
                    data: {
                        payment_method_id: id
                    },
                    success: function(data) {
                        var payment_method_attributes = data.payment_methods;
                        $("#payement-method-attributes").html(payment_method_attributes);

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
    </script>
    </script>
</body>

</html>
