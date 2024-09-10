@include('school.partials.header')



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

<div id="wrapper">

    <!-- Navigation top section -->
    @include('school.partials.navbar')
    <!-- end Navigation top section -->
    <!-- Side naviagtion section -->
    @include('school.partials.sidebar')
    <!-- end Side naviagtion section -->
    @yield('content')
</div>
<!-- end Page info -->
@include('school.partials.footer')
