@include('admin.partials.header')

<body>
    <!-- WRAPPER -->
    <div id="wrapper">
        <!-- Page preloader -->
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
        <!-- end Page preloader -->
        <!-- ============================================================== -->
        <!-- Preloader -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>

        <!-- NAVBAR -->

        @include('admin.partials.navbar')

        <!-- END NAVBAR -->

        <!-- LEFT SIDEBAR -->
        @include('admin.partials.sidebar')
        <!-- END LEFT SIDEBAR -->
        <!-- MAIN -->
        @yield('content')
        <!-- END MAIN -->
        <div class="clearfix"></div>
        @include('admin.partials.footer')
