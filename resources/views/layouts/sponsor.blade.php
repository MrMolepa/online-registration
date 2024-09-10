@include('sponsor.partials.header')
<body class="hold-transition sidebar-mini">
    <div class="wrapper') }}">
      <!-- Navbar -->
      @include('sponsor.partials.navbar')
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      @include('sponsor.partials.sidebar')
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        @yield('content')
      </div>
      <!-- /.content-wrapper -->
      @include('sponsor.partials.footer')
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->







<!-- jQuery -->
<script src="{{ asset('sponsors/assets/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('sponsors/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('sponsors/assets/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('sponsors/assets/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('sponsors//assets/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('sponsors//assets/sparklines/sparkline.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('sponsors/assets/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('sponsors/assets/js/demo.js') }}"></script>
<!-- Page specific script -->

    <!-- App scripts -->
    @stack('scripts')
    @yield('scripts')
</body>

</html>
