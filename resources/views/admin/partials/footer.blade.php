<!-- Javascript -->
<footer>
    <div class="container-fluid">
        <script>
            document.write(new Date().getFullYear());
        </script>
        Examinations Council of Lesotho. All rights reserved.</p>
    </div>
</footer>
</div>

<!-- END WRAPPER -->
<!-- Javascript -->
<script src="{{ asset('adminAssets/assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('adminAssets/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/popper.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/Chart.min.js') }}"></script>
  <!-- jQuery UI -->
  <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js" ></script>


<script type="text/javascript" src="{{ asset('adminAssets/assets/vendor/daterangepicker/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminAssets/assets/vendor/daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('adminAssets/assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="{{ asset('adminAssets/assets/vendor/Chart.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminAssets/assets/scripts/main.js') }}"></script>
<script src="{{ asset('adminAssets/assets/vendor/toastr.min.js') }}"></script>

<!-- App scripts -->
@stack('scripts')
@yield('script')
</body>

</html>
