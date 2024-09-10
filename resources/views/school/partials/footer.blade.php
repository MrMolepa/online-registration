


<!--   Core JS Files   -->
<script src="{{ asset('school/assets/js/core/jquery.min.js') }}"></script>
<!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
<script type="text/javascript" src=" {{asset('school/assets/js/dataTables/datatables.min.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript"
src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/datatables.min.js">
</script>

<script src="{{ asset('school/assets/js/core/popper.min.js') }} "></script>

<!-- Bootstrap Js -->
<script src="{{ asset('school/assets/js/bootstrap.min.js') }} "></script>

<!-- Forms Validations Plugin -->
<script src="{{ asset('school/assets/js/plugins/jquery.validate.min.js') }} "></script>


<!--  Notifications Plugin  toastr  -->
<script src="{{ asset('school/assets/js/toastr.min.js') }}"></script>


{{-- <script src="../assets/js/testlogin.js"></script> --}}

<!-- Main js -->
<script src="{{ asset('school/assets/js/main.js') }}"></script>

<!-- App scripts -->
@stack('scripts')

@yield('script')
</body>

</html>
