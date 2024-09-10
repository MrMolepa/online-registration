@include('candidate.partials.header')
<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    @include('candidate.partials.navbar')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:../../partials/_sidebar.html -->
      @include('candidate.partials.sidebar')
      <!-- partial -->
      <div class="main-panel">
        @yield('content')
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        @include('candidate.partials.footer')

        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
<!-- plugins:js -->


<script src="{{ asset('candidates/vendors/base/vendor.bundle.base.js')}}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js'></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('school/assets/js/plugins/jquery.validate.min.js') }} "></script>
<script src="{{ asset('candidates/js/off-canvas.js')}}"></script>
<script src="{{ asset('candidates/js/hoverable-collapse.js')}}"></script>
<script src="{{ asset('candidates/js/template.js')}}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ asset('candidates/js/file-upload.js')}}"></script>

<!--  Notifications Plugin  toastr  -->
<script src="{{ asset('school/assets/js/toastr.min.js') }}"></script>
<!-- End custom js for this page-->
<script src="{{ asset('candidates/js/verificationForm.js')}}"></script>
<!-- App scripts -->
@stack('scripts')

</body>

</html>
