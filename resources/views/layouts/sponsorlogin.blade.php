<!Doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('sponsors/assets/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('sponsors/assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('sponsors/assets/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b>ECoL</b>Sponsors</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            @yield('content')
        </div>
    </div>
    <!-- /.login-box -->
    <!-- jQuery -->
    <script src="{{ asset('sponsors/assets/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('sponsors/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('sponsors/assets/js/adminlte.min.js') }}"></script>
    <!-- App scripts -->
    @stack('scripts')
    @yield('scripts')
</body>

</html>


















{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href=" {{ asset('sponsors/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sponsors/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('sponsors/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('sponsors/css/colors/default.css') }}" id="colorSkinCSS">

    <!-- CSRF Token -->
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>

<body>
    <div class="main_content_iner pt-5 ">
        <div class="container-fluid pt-5">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('sponsors/js/jquery1-3.4.1.min.js') }}"></script>

    <script src="{{ asset('sponsors/js/popper1.min.js') }}"></script>

    <script src="{{ asset('sponsors/js/bootstrap1.min.js') }}"></script>
    <script src="{{ asset('sponsors/vendors/count_up/jquery.waypoints.min.js') }}"></script>









</body>

</html> --}}
