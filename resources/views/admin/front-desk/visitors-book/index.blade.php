@extends('layouts.admin')

@section('title', 'Visitors Book')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Visitors Book</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addVisitorsBtn">
                                        <i class="fa fa-plus"></i> Visitor
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-hover table-sm" id="visitorsBookTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Purpose</th>
                                                    <th>Meeting With</th>
                                                    <th>Visitor Name</th>
                                                    <th>Phone</th>
                                                    <th>Number of People</th>
                                                    <th>Date</th>
                                                    <th>In Time</th>
                                                    <th>Out Time</th>
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

@include('admin.front-desk.visitors-book._form')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">



@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>



<script>

    $(document).ready(function() {
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

        $(function() {
    // Initialize timepicker for any input with .timepicker class
    $('.timepicker').timepicker({
        timeFormat: 'HH:mm',  // 24-hour format
        interval: 15,         // intervals of 15 minutes
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
});

        let table = $('#visitorsBookTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.front-desk.visitors-book.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'purpose', name: 'purpose' },
                { data: 'meeting_with', name: 'meeting_with' },
                { data: 'visitor_name', name: 'visitor_name' },
                { data: 'phone', name: 'phone' },
                { data: 'number_of_person', name: 'number_of_people' },
                { data: 'date', name: 'date' },
                { data: 'in_time', name: 'in_time' },
                { data: 'out_time', name: 'out_time' },
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
        $('#addVisitorsBtn').click(function() {
            $('#visitorsBookForm')[0].reset();
            $('#visitorsBook_id').val('');
            $('#visitorsBookModalTitle').text('Add Visitor');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#visitorsBookForm').attr('action', '{{ route("admin.front-desk.visitors-book.store") }}');
            
            // Re-initialize datepickers after form reset
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $(function() {
    // Initialize timepicker for any input with .timepicker class
    $('.timepicker').timepicker({
        timeFormat: 'HH:mm',  // 24-hour format
        interval: 15,         // intervals of 15 minutes
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
});
            
            $('#visitorsBookModal').modal('show');
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
                        const log = response.data;
                        fillForm(log, '#visitorsBookForm');
                        $('#visitorsBook_id').val(log.id);
                        $('#visitorsBookModalTitle').text('Edit Visitor');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#visitorsBookForm').attr('action', response.url);
                        
                        $('#visitorsBookModal').modal('show');
                    } else {
                        toastr.error('Sorry. Visitor not found.');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error fetching visitor data. Please try again.');
                }
            });
        });

        // Submit Add/Edit
        $('#visitorsBookForm').submit(function(e) {
            e.preventDefault();
            let id = $('#visitorsBook_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#visitorsBookForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#visitorsBookModal').modal('hide');
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
                        toastr.error(xhr.responseJSON?.message || 'Sorry. An error occurred while saving the visitor.');
                    }
                }        
            });
        });

        // Delete Phone Call Log
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('You are about to delete a visitor record. This action cannot be undone. Are you sure you want to proceed?')) {
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
                    toastr.error(xhr.responseJSON?.message || 'Error deleting visitor record');
                }
            });
        });
    });

    
</script>
@endpush