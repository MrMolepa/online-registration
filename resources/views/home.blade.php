<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Examinations Council of Lesotho</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.min.js"></script>
    <script src="https://portal.nedsecure.co.za/scripts/jquery/js/jquery.litebox.js"></script>
    <script>
        liteboxInitialise('https://portal.nedsecure.co.za', "eserviceform");
    </script>

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
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/css/intlTelInput.css'>

</head>

<body>
    <div class="header-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5   col-lg-4 col-xl-3">
                    <div class="logo">
                        <a href="">
                            <img src="assets/images/logo.png"
                                alt=""style="position: static; top: 0px; width: 3.3rem; padding: 0px;"
                                class="logo">
                        </a>

                    </div>
                </div>
                <div class="col-xl-7 col-lg-8">
                    <div class="main-menu d-none d-lg-block">
                        <nav>

                        </nav>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mobile_menu d-block d-lg-none">
                        <div class="slicknav_menu"><a href="#" aria-haspopup="true" tabindex="0"
                                class="slicknav_btn slicknav_collapsed" style="outline: none;"><span
                                    class="slicknav_menutxt">MENU</span><span class="slicknav_icon"><span
                                        class="slicknav_icon-bar"></span><span class="slicknav_icon-bar"></span><span
                                        class="slicknav_icon-bar"></span></span></a>
                            <ul class="slicknav_nav slicknav_hidden" aria-hidden="true" role="menu"
                                style="display: none;">
                                <li><a class="active" href="/" role="menuitem" tabindex="-1">Home</a>
                                </li>
                                <li><a href="/services" role="menuitem" tabindex="-1">Services</a></li>
                                <li class="slicknav_collapsed slicknav_parent"><a href="#" role="menuitem"
                                        aria-haspopup="true" tabindex="-1" class="slicknav_item slicknav_row"
                                        style="outline: none;"><a href="#" tabindex="-1">Examinations<i
                                                class="bx bx-chevron-down dropdown_icon"></i></a><span
                                            class="slicknav_arrow">►</span></a>
                                    <ul class="submenu slicknav_hidden" role="menu" aria-hidden="true"
                                        style="display: none;">
                                        <li><a href="/examinations-fees" role="menuitem" tabindex="-1">Fees</a>
                                        </li>
                                        <li> <a href="/timetables" role="menuitem" tabindex="-1">TimeTables</a>
                                        </li>
                                        <li><a href="/results" role="menuitem" tabindex="-1">Results &amp;
                                                Statistics</a></li>
                                        <li><a href="/syllabus" role="menuitem" tabindex="-1">Syllabus</a></li>
                                        <li><a href="/programmes" role="menuitem" tabindex="-1">Programmes</a>
                                        </li>
                                        <li><a href="/examiners-report" role="menuitem" tabindex="-1"> Examiner
                                                Reports</a></li>
                                        <li> <a href="/past-question-paper" role="menuitem" tabindex="-1">Past
                                                Question Paper</a></li>
                                    </ul>
                                </li>
                                <li><a href="/opportunities" role="menuitem" tabindex="-1">Opportunities</a>
                                </li>
                                <li class="slicknav_collapsed slicknav_parent"><a href="#" role="menuitem"
                                        aria-haspopup="true" tabindex="-1" class="slicknav_item slicknav_row"
                                        style="outline: none;"><a href="#" tabindex="-1">Publications <i
                                                class="bx bx-chevron-down dropdown_icon"></i></a><span
                                            class="slicknav_arrow">►</span></a>
                                    <ul class="submenu slicknav_hidden" role="menu" aria-hidden="true"
                                        style="display: none;">
                                        <li><a href="/media-release" role="menuitem" tabindex="-1">Media
                                                releases</a></li>
                                        <li><a href="/newsletters" role="menuitem" tabindex="-1">News letter</a>
                                        </li>
                                        <li><a href="/documents" role="menuitem" tabindex="-1">Documents</a>
                                        </li>
                                        <li><a href="/gallery" role="menuitem" tabindex="-1">Gallery</a></li>
                                    </ul>
                                </li>
                                <li class="slicknav_collapsed slicknav_parent"><a href="#" role="menuitem"
                                        aria-haspopup="true" tabindex="-1" class="slicknav_item slicknav_row"
                                        style="outline: none;"><a href="#" tabindex="-1">About Us <i
                                                class="bx bx-chevron-down dropdown_icon"></i></a><span
                                            class="slicknav_arrow">►</span></a>
                                    <ul class="submenu slicknav_hidden" role="menu" aria-hidden="true"
                                        style="display: none;">
                                        <li><a href="/about-us" role="menuitem" tabindex="-1">About Us</a></li>
                                        <li class="slicknav_collapsed slicknav_parent"><a href="#"
                                                role="menuitem" aria-haspopup="true" tabindex="-1"
                                                class="slicknav_item slicknav_row" style="outline: none;"><a
                                                    href="" tabindex="-1">Departments <i
                                                        class="bx bx-chevron-right"></i></a><span
                                                    class="slicknav_arrow">►</span></a>
                                            <ul class="submenu slicknav_hidden" role="menu" aria-hidden="true"
                                                style="display: none;"></ul>
                                        </li>
                                        <li><a href="/executive" role="menuitem" tabindex="-1">Governance</a>
                                        </li>
                                        <li><a href="/contact-us" role="menuitem" tabindex="-1">Contact us</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main>
        <div class="container">
            <div class="registration_choice">
                <div class="grid-wrapper grid-col-auto">
                    <label for="private-candidate" class="radio-card">
                        <input type="radio" name="registration" value="3" id="private-candidate" />
                        <div class="card-content-wrapper">
                            <span class="check-icon"></span>
                            <div class="card-content">
                                <h4>Private Candidate</h4>
                            </div>
                        </div>
                    </label>
                    <label for="school-candidate" class="radio-card">
                        <input type="radio" name="registration" value="2" id="school-candidate" />
                        <div class="card-content-wrapper">
                            <span class="check-icon"></span>
                            <div class="card-content">
                                <h4>School Candidate</h4>
                            </div>
                        </div>
                    </label>
                    <!-- /.radio-card -->

                    <label for="school-admin" class="radio-card">
                        <input type="radio" name="registration" value="1" id="school-admin" />
                        <div class="card-content-wrapper">
                            <span class="check-icon"></span>
                            <div class="card-content">
                                <h4>School Admin</h4>
                            </div>
                        </div>
                    </label>
                    <!-- /.radio-card -->
                </div>
            </div>
        </div>

    </main>

    <section class="about">
        <p class="about-links">
            <a href="http://control-webpanel.com" target="_parent">Visit Website</a>
            <a href="http://control-webpanel.com/installation-instructions" target="_parent">How to Install</a>
        </p>
        <p class="about-author">
            © 2024 <a href="https://control-webpanel.com" target="_blank">Control Web Panel</a>
            control panel for linux
        </p>
    </section>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/services.js') }}"></script>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/additional-methods.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    @stack('scripts')
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
