@extends('layouts.admin')

@section('title', 'Postal Dispatches')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Postal Dispatches</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel panel-headline">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" id="addPostalDispatchBtn">
                                            <i class="fa fa-plus"></i> Postal Dispatch
                                        </button>

                                        <div class="mt-3">
                                            <table class="table table-striped" id="postalDispatchTable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>To</th>
                                                        <th>Reference No</th>
                                                        <th>Address</th>
                                                        <th>From</th>
                                                        <th>Date</th>
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

    @include('admin.front-desk.postal-dispatch._form')

@endsection

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize datepicker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            let table = $('#postalDispatchTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.front-desk.postal-dispatch.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'to', name: 'to' },
                    { data: 'reference_no', name: 'reference_no' },
                    { data: 'address', name: 'address' },
                    { data: 'from', name: 'from' },
                    { data: 'date', name: 'date' },
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
                $.each(data, function (key, value) {
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
            $('#addPostalDispatchBtn').click(function () {
                $('#postalDispatchForm')[0].reset();
                $('#postalDispatch_id').val('');
                $('#postalDispatchModalTitle').text('Add Postal Dispatch');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#postalDispatchForm').attr('action', '{{ route("admin.front-desk.postal-dispatch.store") }}');

                // Re-initialize datepickers after form reset
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });

                $('#postalDispatchModal').modal('show');
            });

            // Open modal for Edit
            $(document).on('click', '.edit-btn', function (e) {
                e.preventDefault();
                let url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        if (response.data) {
                            const log = response.data;
                            fillForm(log, '#postalDispatchForm');
                            $('#postalDispatch_id').val(log.id);
                            $('#postalDispatchModalTitle').text('Edit Phone Call Log');
                            $('.form-control').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').text('');
                            $('#postalDispatchForm').attr('action', response.url);

                            $('#postalDispatchModal').modal('show');
                        } else {
                            toastr.error('Phone call log not found');
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Error fetching postal dispatch data');
                    }
                });
            });

            // Submit Add/Edit
            $('#postalDispatchForm').submit(function (e) {
                e.preventDefault();
                let id = $('#postalDispatch_id').val();
                let method = id ? 'PUT' : 'POST';
                let url = $('#postalDispatchForm').attr('action');

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#postalDispatchModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            printErrorMsg('#postalDispatchForm', errors)
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Error saving postal dispatch');
                        }
                    }
                });
            });

            // Delete Phone Call Log
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();

                if (!confirm('You are about to delete a postal dispatch. This action cannot be undone. Are you sure you want to proceed?')) {
                    return;
                }

                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        table.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error deleting postal');
                    }
                });
            });


            function printErrorMsg(parent, msg) {
                // Clear old errors
                $(`${parent} .help-block`).remove();
                $(`${parent} .has-error`).removeClass('has-error');
                $.each(msg, function (key, errors) {
                    errors.forEach(function (value) {
                        // ARRAY FIELDS  (name="something[]")
                        let arrayFields = $(`${parent} [name='${key}[]']`);
                        if (arrayFields.length) {

                            arrayFields.each(function () {
                                $(this).closest('.form-group').addClass('has-error');

                                $(`<span class='help-block text-danger'>${value}</span>`)
                                    .insertAfter($(this));
                            });

                        }
                        // NORMAL FIELDS (name="something")
                        else {

                            let field = $(`${parent} [name='${key}']`);

                            field.each(function () {
                                $(this).closest('.form-group').addClass('has-error');

                                $(`<span class='help-block text-danger'>${value}</span>`)
                                    .insertAfter($(this));
                            });
                        }

                    });
                });
            }



        });
    </script>
@endpush