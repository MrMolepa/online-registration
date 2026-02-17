@extends('layouts.admin')

@section('title', 'Postal Receive')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Postal Receive</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addPostalReceiveBtn">
                                        <i class="fa fa-plus"></i> Postal Receive
                                    </button>
                                                                            {{-- Hey. I am going to make a very small change. --}}

                                    <div class="mt-3">
                                        <table class="table table-striped" id="postalReceiveTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Package Name</th>
                                                    <th>From</th>
                                                    <th>To</th>
                                                    <th>Reference Number</th>
                                                    <th>Date Received</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PANEL NO CONTROLS -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MAIN CONTENT -->

@include('admin.front-desk.postal-receive._form')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table = $('#postalReceiveTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.front-desk.postal-receive.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'package_name', name: 'package_name'},
                {data: 'from_title', name: 'from_title'},
                {data: 'to_title', name: 'to_title'},
                {data: 'reference_number', name: 'reference_number'},
                {data: 'date_received', name: 'date_received'},
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Helper function to fill form fields
        function fillForm(data, formId) {
            const form = $(formId);
            $.each(data, function(key, value) {
                let field = $('[name="' + key + '"]');
                
                if (field.is(':checkbox')) {
                    field.prop('checked', !!value);
                } else if (field.is(':radio')) {
                    $('input[name="' + key + '"][value="' + value + '"]').prop('checked', true);
                } else {
                    field.val(value);
                }
            });
        }

        // Open modal for Add
        $('#addPostalReceiveBtn').click(function() {
            $('#postalReceiveForm')[0].reset();
            $('#postalReceive_id').val('');
            $('#postalReceiveModalTitle').text('Add Postal Receive');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#postalReceiveForm').attr('action', '{{ route("admin.front-desk.postal-receive.store") }}');
            
            // Re-initialize datepickers after form reset
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            $('#postalReceiveModal').modal('show');
        });

        // Open modal for Edit
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        const postal = response.data;
                        fillForm(postal, '#postalReceiveForm');
                        $('#postalReceive_id').val(postal.id);
                        $('#postalReceiveModalTitle').text('Edit Postal Receive');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#postalReceiveForm').attr('action', response.url);
                        
                        // Re-initialize datepickers
                        $('.datepicker').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true
                        });
                        
                        $('#postalReceiveModal').modal('show');
                    } else {
                        toastr.error('Postal receive not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading postal receive data');
                }
            });
        });

        // Submit Add/Edit
        $('#postalReceiveForm').submit(function(e) {
            e.preventDefault();
            let id = $('#postalReceive_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#postalReceiveForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#postalReceiveModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).siblings('.invalid-feedback').text(value[0]);
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Error saving postal receive');
                    }
                }
            });
        });

        // Delete Postal Receive
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this postal receive?')) {
                return;
            }
            
            let url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting postal receive');
                }
            });
        });
    });
</script>
@endpush