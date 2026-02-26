<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>eServices Portal | Examinations Council of Lesotho</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description"
        content="Official eServices portal for Examinations Council of Lesotho. Apply for certificates, transcripts, and other examination services.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Third-party Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/css/intlTelInput.css'>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        /* ===== GLOBAL VARIABLES ===== */
        :root {
            --primary: #1a5f7a;
            --primary-dark: #0e4a63;
            --accent-color: #57cc99;
            --secondary: #2d8ba7;
            --success: #2a9d8f;
            --danger: #e63946;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --border: #e2e8f0;
            --radius: 8px;
            --shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.2s ease;
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #f5f7fa 0%, #e9f0f5 100%);
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        /* ===== HEADER ===== */
        #logo_container {
            background: rgb(61, 85, 109);
            padding: 1.5rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: -1.5rem;
        }

        .logo {
            height: 70px;
            width: auto;
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.04);
        }

        .info-online {
            text-align: center;
        }

        .info-online h2 {
            font-weight: 700;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 0.25rem;
        }

        .info-online p {
            font-size: 1.2rem;
            color: white;
            font-weight: 500;
        }

        /* ===== MAIN CARD ===== */
        #eservices {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            margin-left: 0;
        }

        #eservices #service-order {
            padding: 18px;
            border: 1px solid #dadada;
        }

        .container {
            max-width: 100%;
        }

        /* ===== PROGRESS STEPS ===== */
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.75rem 2rem;
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            cursor: default;
            transition: opacity 0.2s;
        }

        .step-item.clickable {
            cursor: pointer;
        }

        .step-indicator {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.6rem;
            transition: var(--transition);
        }

        .step-item.active .step-indicator {
            background: var(--primary);
            color: white;
            box-shadow: 0 6px 12px rgba(26, 95, 122, 0.2);
        }

        .step-item.completed .step-indicator {
            background: var(--success);
            color: white;
        }

        .step-number {
            display: inline-block;
        }

        .step-check {
            display: none;
            font-size: 1.2rem;
        }

        .step-item.completed .step-number {
            display: none;
        }

        .step-item.completed .step-check {
            display: inline-block;
        }

        .step-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            text-align: center;
            line-height: 1.3;
        }

        .step-item.completed .step-label {
            color: var(--success);
        }

        .step-connector {
            flex: 0 0 30px;
            height: 2px;
            background: var(--border);
            margin: 0 0.5rem;
            transform: translateY(-12px);
        }

        .step-connector.active {
            background: var(--success);
        }

        /* ===== FORM SECTIONS ===== */
        [role="eservice-tabpanel"] {
            padding: 2rem;
        }

        .form__field {
            margin-bottom: 1.5rem;
        }

        .form__field label {
            color: var(--label-text-color);
            font-family: var(--label-text-font-family);
            font-size: var(--label-text-font-size);
            font-weight: var(--label-text-font-weight);
            display: block;
            letter-spacing: var(--label-text-letter-spacing);
            line-height: 1.6;
            padding-bottom: calc(var(--space-multiplier) * 1rem);
            position: relative;
        }

        .form__field label span[data-required="true"]::after {
            content: " *";
            color: var(--danger);
        }

        .form-control,
        .form-select,
        .select2-selection {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.7rem 1rem;
            height: calc(2.5rem + 10px);
            font-size: 0.95rem;
            transition: var(--transition);
            margin-top: 0.5rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 95, 122, 0.1);
        }

        /* ===== SERVICE CARDS ===== */
        .service-card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: var(--transition);
            background: white;
        }

        .service-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .service-card.selected {
            border-color: var(--primary);
            background: rgba(26, 95, 122, 0.04);
        }

        .service-card h5 {
            color: var(--primary);
            font-weight: 600;
        }

        .service-card .price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--success);
        }

        /* ===== INVOICE SIDEBAR ===== */
        .col-lg-8,
        .col-lg-4 {
            overflow: hidden;
        }

        #service-order {
            overflow-x: auto;
            word-wrap: break-word;
            height: 100%;
        }

        #service-table {
            width: 100%;
            table-layout: fixed;
        }

        #service-table th {
            background: var(--primary);
            color: white;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        #service-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        #service-table .total-row {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        /* ===== PAYMENT OPTIONS ===== */
        .payment-method {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .input-group {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
        }

        .input-group .form-control {
            flex: 1 1 auto;
            width: 1%;
            height: calc(2.5rem + 10px);
            margin-top: 0;
        }

        .input-group .input-group-text {
            border: 1px solid var(--border);
            border-radius: var(--radius) 0 0 var(--radius);
            background: #f1f5f9;
            color: var(--gray);
            padding: 0.7rem 1rem;
            height: calc(2.5rem + 10px);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .payment-option {
            flex: 1 1 140px;
            text-align: center;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-option:hover {
            border-color: var(--primary);
            background: rgba(26, 95, 122, 0.02);
        }

        .payment-option.selected {
            border-color: var(--primary);
            background: rgba(26, 95, 122, 0.04);
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            height: 3.5rem;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 0 1.8rem;
            width: auto;
            font-weight: 600;
            border-radius: 10px;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(26, 95, 122, 0.25);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 0 1.8rem;
            width: auto;
            font-weight: 600;
            border-radius: 10px;
            transition: var(--transition);
        }

        .btn-outline-secondary:hover {
            background: var(--primary);
            color: white;
        }

        /* ===== THANK YOU ===== */
        #progress-form__thank-you {
            padding: 3rem;
            text-align: center;
        }

        .success-icon {
            color: var(--success);
            font-size: 4.5rem;
            margin-bottom: 1.2rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            #eservices {
                margin: 2rem;
            }

            [role="eservice-tabpanel"] {
                padding: 1.5rem;
            }

            #service-order {
                border-left: none;
                border-top: 1px solid var(--border);
            }

            .progress-steps {
                padding: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .progress-steps {
                flex-direction: column;
                align-items: stretch;
                padding: 1rem;
            }

            .step-item {
                flex-direction: row;
                justify-content: flex-start;
                gap: 1rem;
                margin-bottom: 0.75rem;
            }

            .step-indicator {
                width: 36px;
                height: 36px;
                font-size: 1rem;
                margin-bottom: 0;
            }

            .step-connector {
                display: none;
            }

            .info-online h2 {
                font-size: 1.4rem;
            }
        }

        /* ===== UTILITY ===== */
        .alert-info {
            background: rgba(26, 95, 122, 0.06);
            border-left: 5px solid var(--primary);
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 1rem 1.5rem;
        }

        .is-invalid {
            border-color: var(--danger) !important;
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        /* ===== INCREASED TEXT SIZE FOR INPUTS ===== */
        .form-control,
        .form-select,
        textarea,
        .input-group-text,
        .select2-selection,
        .select2-search__field,
        .intl-tel-input input,
        .iti__tel-input {
            font-size: 1.2rem;
            padding: 0 1rem;
        }

        .select2-selection--single,
        .select2-selection--multiple {
            height: auto !important;
            min-height: 48px;
        }

        ::placeholder {
            font-size: 1.2rem;
            opacity: 0.7;
        }
    </style>
</head>

<body>
    <main>

        <!-- ===== HEADER ===== -->
        <section id="logo_container">
            <div class="container" style="width:100%">
                <div class="row align-items-left">
                    <div class="col-md-3 text-center text-md-start">
                        <div class="logo_box">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Examinations Council of Lesotho Logo"
                                class="logo">
                        </div>
                    </div>
                    <div class="col-md-9" style="text-align:left">
                        <div style="align-items:left;display:flex">
                            <div class="info-online">
                                <h2>Examinations Council of Lesotho</h2>
                                <p>eServices Portal</p>
                                <small class="text-muted">Secure online services for examination certificates and
                                    transcripts</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== MAIN FORM ===== -->
        <div class="container" id="eservices">

            <div class="alert-info">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Important:</strong> Please complete all required fields and ensure your information is accurate
                before submission.
            </div>
            <div style="padding-bottom:1.2rem"></div>

            <form class="row g-0" id="eserviceform" method="POST" action="{{ route('services.multiform') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="total_sale_price" id="total_sale_price" value="">
                <input type="hidden" name="sigle_sale_price" id="sigle_sale_price" value="">

                <!-- ===== LEFT COLUMN ===== -->
                <div class="col-lg-8">

                    <!-- Step navigation -->
                    <div class="progress-steps">
                        <div class="step-item active" data-step="1" aria-current="step">
                            <div class="step-indicator">
                                <span class="step-number">1</span>
                                <i class="fas fa-check step-check"></i>
                            </div>
                            <span class="step-label">Personal Info</span>
                        </div>
                        <div class="step-connector"></div>
                        <div class="step-item" data-step="2">
                            <div class="step-indicator">
                                <span class="step-number">2</span>
                                <i class="fas fa-check step-check"></i>
                            </div>
                            <span class="step-label">Requirements</span>
                        </div>
                        <div class="step-connector"></div>
                        <div class="step-item" data-step="3">
                            <div class="step-indicator">
                                <span class="step-number">3</span>
                                <i class="fas fa-check step-check"></i>
                            </div>
                            <span class="step-label">Payments</span>
                        </div>
                    </div>

                    <!-- STEP 1 -->
                    <section id="progress-form__panel-1" role="eservice-tabpanel" style="padding:2rem"
                        aria-labelledby="progress-form__tab-1">
                        <h4 class="mb-4 text-primary"><i class="fas fa-user-circle me-2"></i>Select Service & Personal
                            Details</h4>

                        <div class="form__field">
                            <label for="select-service">
                                <i class="fas fa-concierge-bell me-2"></i>Select Service Type
                                <span data-required="true" aria-hidden="true"></span>
                            </label>
                            <select id="select-service" name="service" class="form-select" required>
                                <option value="" disabled selected>-- Choose a service --</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->desciption }}</option>
                                @endforeach
                                <option value="status">Check Application Status</option>
                            </select>
                            <small class="text-muted">Choose the service you require from the list above</small>
                        </div>

                        <div id="service-items-container" class="mt-4"></div>
                        <div id="personal-info" class="mt-4"></div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn-primary" id="next-to-step-2" disabled>
                                Continue <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 2 -->
                    <section id="progress-form__panel-2" role="eservice-tabpanel" style="padding:2rem"
                        aria-labelledby="progress-form__tab-2" hidden>
                        <h4 class="mb-4 text-primary"><i class="fas fa-file-alt me-2"></i>Requirements</h4>
                        <div id="requirements-container"></div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn-outline-secondary" id="back-to-step-1">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn-primary" id="next-to-step-3">
                                Continue <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </section>

                    <!-- STEP 3 -->
                    <section id="progress-form__panel-3" role="eservice-tabpanel" aria-labelledby="progress-form__tab-3"
                        hidden>
                        <h4 class="mb-4 text-primary"><i class="fas fa-credit-card me-2"></i>Payments</h4>

                        <div class="payment-method">
                            <div class="payment-option" data-payment="credit-card">
                                <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                                <div class="payment-name">Credit/Debit Card</div>
                                <small class="text-muted">Secure online payment</small>
                            </div>
                            <div class="payment-option" data-payment="eco-cash">
                                <div class="payment-icon"><i class="fas fa-mobile-alt"></i></div>
                                <div class="payment-name">EcoCash</div>
                                <small class="text-muted">Mobile money</small>
                            </div>
                            <div class="payment-option" data-payment="mpesa">
                                <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                                <div class="payment-name">M-PESA</div>
                                <small class="text-muted">Mobile money</small>
                            </div>
                            <div class="payment-option" data-payment="bank-deposit">
                                <div class="payment-icon"><i class="fas fa-university"></i></div>
                                <div class="payment-name">Bank Deposit</div>
                                <small class="text-muted">Upload proof</small>
                            </div>
                        </div>

                        <div id="payment-details"></div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn-outline-secondary" id="back-to-step-2">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button id="btn-submit" type="submit" class="btn-primary" disabled>
                                <i class="fas fa-paper-plane me-2"></i> Submit Application
                            </button>
                        </div>
                    </section>

                    <!-- THANK YOU -->
                    <section id="progress-form__thank-you" hidden>
                        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
                        <h2 class="text-success">Application Submitted Successfully!</h2>
                        <p class="lead">Your application has been received and is being processed.</p>
                        <div class="alert alert-light border my-4">
                            <h5><i class="fas fa-receipt me-2"></i>Reference Number</h5>
                            <p class="reference_number h4 text-primary">Loading...</p>
                            <small class="text-muted">Please keep this number for future reference and tracking</small>
                        </div>
                        <p>You will receive a confirmation email with details of your application and tracking
                            information.</p>
                        <p>For any inquiries, please contact <a
                                href="mailto:eservices@examinations.org.ls">eservices@examinations.org.ls</a></p>
                        <div class="mt-4">
                            <a href="/services" class="btn-primary me-2"><i class="fas fa-home me-2"></i> Back to
                                Home</a>
                            <button id="new-application" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i> Start New Application
                            </button>
                        </div>
                    </section>

                </div>{{-- /col-lg-8 --}}

                <!-- ===== RIGHT COLUMN: INVOICE ===== -->
                <div class="col-lg-4" style="padding-left:0">
                    <div
                        style="background:var(--primary);height:4rem;align-items:center;display:flex;justify-content:center">
                        <h class="text-white">Your Invoice</h>
                    </div>
                    <div id="service-order">
                        <div id="service-table-container">
                            <table id="service-table" class="table">
                                <tbody>
                                    <tr>
                                        <td class="text-center text-muted py-4">
                                            <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                                            <p>No services selected yet</p>
                                            <small>Select a service to see details here</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2"><span>Processing time:</span><span
                                    class="fw-bold">5-7 working days</span></div>
                            <div class="d-flex justify-content-between mb-2"><span>Service availability:</span><span
                                    class="text-success">24/7 Online</span></div>
                            <div class="d-flex justify-content-between"><span>Support:</span><span><a
                                        href="tel:+26622317272">+266 2231 7272</a></span></div>
                        </div>
                        <div class="mt-4">
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> All fees are non-refundable once processing begins.
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </main>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/intlTelInput.min.js'></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- LiteBox (iVeri) -->
    <div id="iveri-litebox" class="center-block"></div>
    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>
    <script>liteboxInitialise('https://portal.nedsecure.co.za', 'eserviceform');</script>

    <script>
        $(document).ready(function () {

            // ==========================================================================
            // CONFIG
            // ==========================================================================
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 5000 };

            /** Shared cart state */
            let selectedService = { id: null, name: '', price: 0, total: 0 };

            // ==========================================================================
            // VALIDATION HELPERS
            // ==========================================================================

            function showFieldError(input, message) {
                const $field = $(input);
                $field.removeClass('is-invalid').next('.invalid-feedback').remove();

                const labelText = ($field.closest('.form__field').find('label').first().text().replace('*', '').trim()
                    || $field.attr('name')
                    || 'This field');

                const label = labelText.charAt(0).toUpperCase() + labelText.slice(1);
                $field.addClass('is-invalid')
                    .after(`<span class="invalid-feedback d-block">${message || label + ' is required.'}</span>`);
            }

            function clearFieldError(input) {
                $(input).removeClass('is-invalid').next('.invalid-feedback').remove();
            }

            function scrollToFirstError(container) {
                const $first = $(container).find('.is-invalid').first();
                if ($first.length) {
                    $('html, body').animate({ scrollTop: $first.offset().top - 120 }, 400);
                    $first.focus();
                }
            }

            // Auto-clear errors on change
            $(document).on('input change', 'input, select, textarea', function () { clearFieldError(this); });

            // ---- Format validators ----
            const Validators = {
                mobile: ($f) => /^\d{8}$/.test($f.val().trim()) || (showFieldError($f, 'Must be exactly 8 digits.'), false),
                phone: ($f) => /^\d{8}$/.test($f.val().trim()) || (showFieldError($f, 'Must be a valid 8-digit Lesotho number.'), false),
                name: ($f) => { const v = $f.val().trim(); return !v || /^[A-Za-z\-']+$/.test(v) || (showFieldError($f, 'Must contain only letters, hyphens or apostrophes (no spaces).'), false); },
                email: ($f) => { const v = $f.val().trim(); return !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || (showFieldError($f, 'Please enter a valid email address.'), false); },
                nationalId: ($f) => /^\d{12}$/.test($f.val().trim()) || (showFieldError($f, 'Must be exactly 12 digits.'), false),
            };

            /** Run field-level format validation by name */
            function validateFieldByName($field) {
                const name = $field.attr('name');
                if (name === 'first_name' || name === 'last_name') return Validators.name($field);
                else if (name === 'email') return Validators.email($field);
                else if (name === 'phone') return Validators.phone($field);
                else if (name === 'national_identity') return Validators.nationalId($field);
                else if (name === 'mpesa_mobile' || name === 'ecocash_mobile') return Validators.mobile($field);
                return true;
            }

            // Real-time validation bindings
            $(document).on('input', '[name="first_name"],[name="last_name"]', function () { Validators.name($(this)); });
            $(document).on('input', '[name="email"]', function () { Validators.email($(this)); });
            $(document).on('input', '[name="phone"]', function () { Validators.phone($(this)); });
            $(document).on('input', '[name="national_identity"]', function () { Validators.nationalId($(this)); });
            $(document).on('input', '[name="mpesa_mobile"],[name="ecocash_mobile"]', function () { Validators.mobile($(this)); });

            // ==========================================================================
            // STEP MANAGER
            // ==========================================================================
            const StepManager = {
                currentStep: 1,
                totalSteps: 3,

                init() {
                    this.attachEvents();
                    this.showStep(this.currentStep);
                },

                showStep(step) {
                    if (step < 1 || step > this.totalSteps) return;
                    this.currentStep = step;
                    $('[role="eservice-tabpanel"]').hide().attr('hidden', true);
                    $(`#progress-form__panel-${step}`).show().removeAttr('hidden');
                    this._updateStepUI();
                },

                goToStep(step) {
                    if (step < this.currentStep || this._validateCurrentPanel()) {
                        this.showStep(step);
                    }
                },

                _validateCurrentPanel() {
                    const $panel = $(`#progress-form__panel-${this.currentStep}`);
                    let valid = true;

                    $panel.find('[required]').each(function () {
                        const $f = $(this);
                        if (!$f.val()) { showFieldError(this); valid = false; return; }
                        if (!validateFieldByName($f)) valid = false;
                    });

                    if (!valid) scrollToFirstError($panel);
                    return valid;
                },

                _updateStepUI() {
                    $('.step-item').removeClass('active completed clickable').attr('aria-current', false);
                    $('.step-connector').removeClass('active');

                    for (let i = 1; i <= this.totalSteps; i++) {
                        const $item = $(`.step-item[data-step="${i}"]`);
                        if (i === this.currentStep) $item.addClass('active').attr('aria-current', 'step');
                        else if (i < this.currentStep) {
                            $item.addClass('completed clickable');
                            $item.next('.step-connector').addClass('active');
                        }
                    }
                },

                attachEvents() {
                    $(document).on('click', '.step-item.completed', (e) => this.goToStep(+$(e.currentTarget).data('step')));
                    $('#next-to-step-2').click(() => this.goToStep(2));
                    $('#next-to-step-3').click(() => this.goToStep(3));
                    $('#back-to-step-1').click(() => this.goToStep(1));
                    $('#back-to-step-2').click(() => this.goToStep(2));
                },
            };

            StepManager.init();

            // ==========================================================================
            // SERVICE LOADING
            // ==========================================================================

            function loadServiceItems(serviceId) {
                $.ajax({
                    url: "{{ route('services.serviceItem') }}",
                    method: 'POST',
                    data: { service: serviceId },
                    beforeSend: () => $('#service-items-container').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>'),
                    success(res) {
                        $('#service-items-container').html(res.html);
                        if (serviceId === 'status') loadStatusCheckForm();
                    },
                    error: () => toastr.error('Failed to load service options.'),
                });
            }

            function loadServiceRequirements() {
                $.ajax({
                    url: "{{ route('services.serviceRequirements') }}",
                    method: 'POST',
                    data: { service: selectedService.id },
                    beforeSend() {
                        $('#personal-info').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
                        $('#requirements-container').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading requirements...</p></div>');
                    },
                    success(res) {
                        $('#personal-info').html(res.personalInfoHTML);
                        $('#requirements-container').html(res.attributesHTML);
                        if (res.paymentsHTML) $('.payment-method').html(res.paymentsHTML);
                        initializePlugins();
                    },
                    error: () => toastr.error('Failed to load service requirements.'),
                });
            }

            // ==========================================================================
            // INVOICE
            // ==========================================================================

            function updateInvoice() {
                const tbody = selectedService.id
                    ? `<tr>
                   <td>${selectedService.name}</td>
                   <td class="text-end">M${selectedService.price.toFixed(2)}</td>
               </tr>
               <tr class="total-row">
                   <td class="fw-bold">Total</td>
                   <td class="fw-bold text-end">M${selectedService.total.toFixed(2)}</td>
               </tr>`
                    : `<tr>
                   <td class="text-center text-muted py-4">
                       <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                       <p>No services selected yet</p>
                       <small>Select a service to see details here</small>
                   </td>
               </tr>`;

                $('#service-table tbody').html(tbody);
            }

            function addToCart(elem) {
                const $el = $(elem);
                selectedService = {
                    id: $el.data('id'),
                    name: $el.data('service') || 'Service',
                    price: parseFloat($el.data('price')) || 0,
                    total: parseFloat($el.data('price')) || 0,
                };
                updateInvoice();
                $('[name="total_sale_price"]').val(selectedService.price);
                $('[name="sigle_sale_price"]').val(selectedService.price);
                $('#next-to-step-2').prop('disabled', false);
            }

            // ==========================================================================
            // PAYMENT HANDLERS
            // ==========================================================================

            $(document).on('click', '#mpesa_pay', function () { submitMobilePayment($(this), 'VclMpesa'); });
            $(document).on('click', '#ecocash_pay', function () { submitMobilePayment($(this), 'EcoCash'); });

            /** Shared AJAX handler for M-PESA and EcoCash */
            function submitMobilePayment($btn, paymentType) {
                const formData = new FormData($('#eserviceform')[0]);
                formData.append('payment', paymentType);
                const caption = $btn.html();

                $.ajax({
                    url: "{{ route('services.transaction') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: () => $btn.prop('disabled', true).html('Processing...'),
                    success(res) {
                        if ($.isEmptyObject(res.errors)) {
                            handleSuccess(res);
                        } else {
                            printErrorMsg('#eserviceform', res.errors);
                        }
                        $btn.prop('disabled', false).html(caption);
                    },
                    error(xhr) {
                        console.error('Payment error:', xhr.responseText);
                        toastr.error('Payment request failed. Check console for details.');
                        $btn.prop('disabled', false).html(caption);
                    },
                });
            }

            /** LiteBox credit card callback */
            window.liteboxComplete = function (data) {
                const formData = new FormData($('#eserviceform')[0]);
                $.each($.parseJSON(data), (k, v) => formData.append(k, v));
                formData.append('payment', 'CreditCard');

                $.ajax({
                    url: "{{ route('services.transaction') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success(res) {
                        $('.modal-backdrop').hide();
                        if ($.isEmptyObject(res.errors)) {
                            handleSuccess(res);
                        } else {
                            swal({ title: 'Error!', text: res.errors['bank_card']?.[0] || 'Payment error', icon: 'error' });
                        }
                    },
                    error: () => toastr.error('Credit card transaction failed.'),
                });
            };

            /** Select payment method, show its detail panel */
            $(document).on('click', '.payment-option', function () {
                $('.payment-option').removeClass('selected');
                $(this).addClass('selected');
                const method = $(this).data('payment');
                loadPaymentDetails(method);
                $('#btn-submit').prop('disabled', method !== 'bank-deposit');
            });

            // ==========================================================================
            // PAYMENT DETAIL PANELS
            // ==========================================================================

            const PaymentPanels = {
                'credit-card': () => `
            <div class="card mt-3"><div class="card-body">
                <h5 class="card-title"><i class="fas fa-lock me-2"></i> Pay with Credit/Debit Card</h5>
                <p class="card-text">You will be redirected to our secure payment gateway.</p>
                <input type="hidden" value="Mr." readonly name="Ecom_BillTo_Postal_Name_Prefix" id="Ecom_BillTo_Postal_Name_Prefix" />
                <input name="Ecom_BillTo_Postal_Name_First" readonly type="hidden" value="John" id="Ecom_BillTo_Postal_Name_First" />
                <input type="hidden" name="Ecom_BillTo_Postal_Name_Middle" id="Ecom_BillTo_Postal_Name_Middle" />
                <input name="Ecom_BillTo_Postal_Name_Last" readonly type="hidden" value="Doe" id="Ecom_BillTo_Postal_Name_Last" />
                <input name="Ecom_BillTo_Online_Email" readonly type="hidden" value="jdoe@mail.com" maxlength="50" id="Ecom_BillTo_Online_Email" />
                <input type="hidden" readonly name="Ecom_ShipTo_Postal_Street_Line1" id="Ecom_ShipTo_Postal_Street_Line1" value="50 Sunny Drive Avenue" />
                <input type="hidden" readonly name="Ecom_ShipTo_Postal_Street_Line2" id="Ecom_ShipTo_Postal_Street_Line2" value="Sunsetville" />
                <input type="hidden" readonly name="Ecom_ShipTo_Postal_City"         id="Ecom_ShipTo_Postal_City"         value="Johannesburg" />
                <input type="hidden" readonly name="Ecom_ShipTo_Postal_StateProv"    id="Ecom_ShipTo_Postal_StateProv"    value="Gauteng" />
                <input type="hidden" readonly name="Ecom_ShipTo_Postal_PostalCode"   id="Ecom_ShipTo_Postal_PostalCode"   value="2185" />
                <input name="Ecom_ConsumerOrderID" readonly type="hidden" value="AUTOGENERATE" maxlength="20" id="Ecom_ConsumerOrderID" />
                <input type="hidden" name="Ecom_SchemaVersion"         id="Ecom_SchemaVersion" />
                <input type="hidden" name="Ecom_TransactionComplete"   id="Ecom_TransactionComplete"   value="false" />
                <input type="hidden" name="Lite_Authorisation"         id="Lite_Authorisation"         value="false" />
                <input type="hidden" name="Lite_Version"               id="Lite_Version"               value="2.0" />
                <input type="hidden" readonly name="Lite_Order_LineItems_Product_1"  id="Lite_Order_LineItems_Product_1"  value="Total Subjects" />
                <input type="hidden" readonly name="Lite_Order_LineItems_Quantity_1" id="Lite_Order_LineItems_Quantity_1" value="1" />
                <input type="hidden" readonly name="Transaction_LineItems_Amount_1"  id="Transaction_LineItems_Amount_1"  value="100" />
                <input type="hidden" name="Lite_Order_LineItems_Amount_1" id="Lite_Order_LineItems_Amount_1" value="100" />
                <input name="Transaction_Amount" id="Transaction_Amount" type="hidden" value="100" />
                <input type="hidden" value="100" name="Lite_Order_Amount" id="Lite_Order_Amount" />
                <input name="Merchant_ApplicationID"      type="hidden" value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}" maxlength="40" id="Merchant_ApplicationID" />
                <input type="hidden" name="Lite_Merchant_ApplicationID"  value="{03872D01-B41E-4D62-862B-D55DAE2CD1D5}" id="Lite_Merchant_ApplicationID" />
                <input type="hidden" name="Ecom_Payment_Card_Protocols"  id="Ecom_Payment_Card_Protocols"  value="iVeri" />
                <input type="hidden" name="Lite_Order_Terminal"          id="Lite_Order_Terminal"          value="77777001" />
                <input type="hidden" name="Lite_Order_AuthorisationCode" id="Lite_Order_AuthorisationCode" />
                <input type="hidden" name="Lite_Website_TextColor"       id="Lite_Website_TextColor"       value="#ffffff" />
                <input type="hidden" name="Lite_Website_BGColor"         id="Lite_Website_BGColor"         value="#fff" />
                <input type="hidden" name="Lite_ConsumerOrderID_PreFix"  id="Lite_ConsumerOrderID_PreFix"  value="LITE" />
                <input type="hidden" name="Lite_Website_Successful_Url"  id="Lite_Website_Successful_Url"  value="https://examples.iveri.net/Lite/Result.asp" />
                <input type="hidden" name="Lite_Website_Fail_Url"        id="Lite_Website_Fail_Url"        value="https://examples.iveri.net/Lite/Result.asp" />
                <input type="hidden" name="Lite_Website_Error_Url"       id="Lite_Website_Error_Url"       value="https://examples.iveri.net/Lite/Result.asp" />
                <input type="hidden" name="Lite_Website_Trylater_Url"    id="Lite_Website_Trylater_Url"    value="https://examples.iveri.net/Lite/Result.asp" />
                <a href="javascript:void(0)" id="iveri-litebox-button" class="btn-primary w-100" style="height:40px;line-height:40px;">
                    <i class="fas fa-credit-card me-2"></i> Proceed to Secure Payment
                </a>
                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="fas fa-info-circle me-2"></i> Visa, Mastercard, and Amex accepted.
                </div>
            </div></div>`,

                'eco-cash': () => `
            <div class="card mt-3"><div class="card-body">
                <h5 class="card-title"><i class="fas fa-mobile-alt me-2"></i> EcoCash</h5>
                <div class="form__field">
                    <label>Mobile Number</label>
                    <div class="input-group" style="width:100%">
                        <span class="input-group-text">+266</span>
                        <input type="tel" class="form-control" name="ecocash_mobile" placeholder="6XXXXXXX" required>
                    </div>
                    <small class="text-muted">Enter the number registered with EcoCash.</small>
                </div>
                <button type="button" id="ecocash_pay" class="btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i> Send Payment Request
                </button>
            </div></div>`,

                'mpesa': () => `
            <div class="card mt-3"><div class="card-body">
                <h5 class="card-title"><i class="fas fa-money-bill-wave me-2"></i> M-PESA</h5>
                <div class="form__field">
                    <label>Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text">+266</span>
                        <input type="tel" class="form-control" name="mpesa_mobile" placeholder="5XXXXXXX" required>
                    </div>
                    <small class="text-muted">Enter the number registered with M-PESA.</small>
                </div>
                <button type="button" id="mpesa_pay" class="btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i> Send Payment Request
                </button>
            </div></div>`,

                'bank-deposit': () => `
            <div class="card mt-3"><div class="card-body">
                <h5 class="card-title"><i class="fas fa-university me-2"></i>Bank Deposit</h5>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Bank Account Details</h6>
                    <p class="mb-1"><strong>Bank:</strong> Standard Lesotho Bank</p>
                    <p class="mb-1"><strong>Account Name:</strong> Examinations Council of Lesotho</p>
                    <p class="mb-1"><strong>Account Number:</strong> 0140000001234</p>
                    <p class="mb-0"><strong>Branch Code:</strong> 070167</p>
                </div>
                <div class="form__field">
                    <label>Proof of Payment (PDF/Image)</label>
                    <input type="file" class="form-control" name="deposit_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <div class="form__field">
                    <label>Deposit Date</label>
                    <input type="date" class="form-control" name="deposit_date" required>
                </div>
                <div class="form__field">
                    <label>Deposit Reference</label>
                    <input type="text" class="form-control" name="deposit_reference"
                           placeholder="Enter reference from deposit slip" required>
                </div>
                <input type="hidden" name="payment" value="BankDeposit">
            </div></div>`,
            };

            function loadPaymentDetails(method) {
                const builder = PaymentPanels[method];
                if (builder) $('#payment-details').html(builder());

                if (method === 'credit-card' && typeof liteboxInitialise === 'function') {
                    liteboxInitialise('https://portal.nedsecure.co.za', 'eserviceform');
                }
            }

            // ==========================================================================
            // LITEBOX FORM SYNC (keep hidden fields in step with live form values)
            // ==========================================================================
            function syncLiteboxFields() {
                const price = ($('[name="total_sale_price"]').val() || 0) * 100;
                $('#Ecom_BillTo_Postal_Name_First').val($('[name="first_name"]').val());
                $('#Ecom_BillTo_Postal_Name_Last').val($('[name="last_name"]').val());
                $('#Ecom_BillTo_Postal_Name_Prefix').val('Miss. or Mrs. or Mr');
                $('#Ecom_BillTo_Online_Email').val($('[name="email"]').val());
                $('#Transaction_Amount, #Transaction_LineItems_Amount_1, #Lite_Order_LineItems_Amount_1, #Lite_Order_Amount').val(price);
                $('#Lite_Order_LineItems_Quantity_1').val(1);
                $('#Lite_Order_LineItems_Product_1').val($('[name="serviceItem"]').val());
            }

            $(document).on('input', 'input', syncLiteboxFields);

            // ==========================================================================
            // SUBJECTS & CENTRE
            // ==========================================================================

            $(document).on("change", ".livesearch-all-centers", function () {
                let centre_no = $(this).val();
                let level = $("#level option:selected").data("level");
                let session = 4;

                if (!centre_no || !level) {
                    $('.subjects_selection').html('');
                    return;
                }

                $.ajax({
                    url: "/registeration-center-subjects",
                    method: "POST",
                    data: { centre_no, level, session },
                    success: function (data) {
                        $(".subjects_selection").html(data.subjectsHTML);
                        $('.subjects_selection input[type="checkbox"]').prop('required', true);
                    },
                    error: () => toastr.error('Failed to load subjects.')
                });
            });

            $(document).on('click', '.subjects_selection input', function () {
                const classList = $(this).attr('class').split(' ');
                const cls = classList.find(c => c.toLowerCase().includes('subj_'));
                if (cls) $(`.${cls}`).prop('checked', false);
                if ($(this).prop('checked')) $(this).prop('checked', true);
                recalculateSubjectTotal();
            });

            function recalculateSubjectTotal() {
                const count = $('.subjects_selection input:checkbox:checked').length;
                const unitPrice = parseFloat($('[name="sigle_sale_price"]').val()) || 0;
                const total = count > 0 ? count * unitPrice : selectedService.price;

                $('#is_subject').val(count > 0 ? count : '');
                $('[name="total_sale_price"]').val(count > 0 ? total : selectedService.price);
                selectedService.total = total;
                updateInvoice();
            }

            // ==========================================================================
            // CANDIDATE VALIDATION
            // ==========================================================================

            $(document).on('keyup', '#candidate_no', function () {
                $.ajax({
                    url: '/services/valid-candidate',
                    method: 'POST',
                    data: { candidate_no: $(this).val() },
                    success(data) {
                        if ($.isEmptyObject(data.errors)) {
                            $('#is_candidate').val(data.candidate_no);
                        } else {
                            printErrorMsg('#eserviceform', data.errors);
                        }
                    },
                    error: () => toastr.error('Candidate validation failed.'),
                });
            });

            // ==========================================================================
            // ERROR DISPLAY
            // ==========================================================================

            function printErrorMsg(parent, errors) {
                $(`${parent} .is-invalid`).removeClass('is-invalid');
                $(`${parent} .invalid-feedback`).remove();
                $.each(errors, function (field, messages) {
                    const $f = $(`${parent} [name="${field}"]`);
                    if ($f.length) showFieldError($f, messages[0]);
                });
                scrollToFirstError(parent);
            }

            // ==========================================================================
            // STATUS CHECK
            // ==========================================================================

            function loadStatusCheckForm() {
                $('#personal-info').html(`
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Check Application Status</h5>
                </div>
                <div class="card-body">
                    <div class="form__field">
                        <label for="reference_no">Reference Number</label>
                        <input type="text" id="reference_no" name="reference_no" class="form-control"
                               placeholder="Enter your application reference number" required>
                        <small class="text-muted">Enter the reference number provided when you submitted your application</small>
                    </div>
                    <button type="button" id="check-status" class="btn-primary mt-3">
                        <i class="fas fa-search me-2"></i> Check Status
                    </button>
                </div>
            </div>`);
            }

            $(document).on('click', '#check-status', function () {
                const refNo = $('#reference_no').val();
                if (!refNo) { toastr.warning('Please enter a reference number'); return; }

                $.ajax({
                    url: "{{ route('services.checkstatus') }}",
                    type: 'POST',
                    data: { reference_no: refNo },
                    success(res) {
                        $('#personal-info, #progress-form__panel-2, .payment-method').empty();
                        if ($.isEmptyObject(res.errors)) {
                            $('.statuses-container').html(res.status);
                            $('.status-progress-wrap').css('display', 'flex');
                        } else {
                            $('.statuses-container').empty();
                            printErrorMsg('#eserviceform', res.errors);
                        }
                    },
                    error: () => toastr.error('Status check failed.'),
                });
            });

            // ==========================================================================
            // SUCCESS / NEW APPLICATION
            // ==========================================================================

            function handleSuccess(response) {
                $('#eserviceform [role="eservice-tabpanel"]').remove();
                $('#eserviceform .progress-steps').remove();
                $('#progress-form__thank-you').removeAttr('hidden');
                $('#btn-submit').prop('disabled', true);
                $('.reference_number').html(response.reference_number);
            }

            $(document).on('click', '#new-application', () => location.reload());

            // ==========================================================================
            // PLUGIN INITIALISATION
            // ==========================================================================

            function initializePlugins() {
                $('.select2').select2({ theme: 'bootstrap-3', width: '100%' });

                const phoneInput = document.querySelector('#phone');
                if (phoneInput) {
                    const iti = intlTelInput(phoneInput, {
                        initialCountry: 'ls',
                        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/utils.js',
                    });
                    $(phoneInput).data('intlTelInput', iti);
                }

                $('.livesearch-all-centers').select2({
                    placeholder: '--Select the Center--',
                    theme: 'bootstrap-3',
                    width: '100%',
                    ajax: {
                        url: "{{ route('services.autocomplete') }}",
                        method: 'POST',
                        dataType: 'json',
                        delay: 250,
                        processResults: (data) => ({
                            results: $.map(data, (item) => ({
                                text: `${item.center_name} (${item.level})`,
                                id: item.center_no,
                            })),
                        }),
                        cache: true,
                    },
                });
            }

            // ==========================================================================
            // SERVICE SELECTION EVENTS
            // ==========================================================================

            $('#select-service').change(function () {
                const id = $(this).val();
                if (id) {
                    loadServiceItems(id);
                    $('#next-to-step-2').prop('disabled', true);
                } else {
                    $('#service-items-container, #personal-info, #requirements-container').empty();
                    selectedService = { id: null, name: '', price: 0, total: 0 };
                    updateInvoice();
                    $('#next-to-step-2').prop('disabled', true);
                }
            });

            $(document).on('click', '[name="serviceItem"]', function () {
                addToCart(this);
                loadServiceRequirements();
            });

            // ==========================================================================
            // FILE VALIDATION
            // ==========================================================================

            $(document).on('change', 'input[type="file"]', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    showFieldError(this, 'File must be less than 2MB');
                    this.value = '';
                } else if (!['image/jpeg', 'image/png', 'application/pdf'].includes(file.type)) {
                    showFieldError(this, 'Only JPG, PNG, or PDF allowed');
                    this.value = '';
                } else {
                    clearFieldError(this);
                }
            });

            // ==========================================================================
            // STARTUP
            // ==========================================================================
            initializePlugins();

        }); // end document.ready
    </script>

</body>

</html>