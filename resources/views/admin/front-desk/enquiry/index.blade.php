@extends('layouts.admin')

@section('title', 'Enquiries')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Enquiries</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addEnquiryBtn">
                                        {{-- Hey. I am going to make a very small change. --}}
                                        <i class="fa fa-plus"></i> Enquiry
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-striped" id="enquiryTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Enquiry Date</th>
                                                    <th>Status</th>
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

@include('admin.front-desk.enquiry._form')
@include('admin.front-desk.enquiry._view')

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

        let table = $('#enquiryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.front-desk.enquiry.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'enquiry_date', name: 'enquiry_date'},
                {data: 'status_badge', name: 'is_active'},
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
        $('#addEnquiryBtn').click(function() {
            $('#enquiryForm')[0].reset();
            $('#enquiry_id').val('');
            $('#enquiryModalTitle').text('Add Enquiry');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#enquiryForm').attr('action', '{{ route("admin.front-desk.enquiry.store") }}');
            
            // Set default active status
            $('#is_active').prop('checked', true);

            
            $('#enquiryModal').modal('show');
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
                        const enquiry = response.data;
                        fillForm(enquiry, '#enquiryForm');
                        $('#enquiry_id').val(enquiry.id);
                        $('#enquiryModalTitle').text('Edit Enquiry');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#enquiryForm').attr('action', response.url);
                        
                        
                        $('#enquiryModal').modal('show');
                    } else {
                        toastr.error('Enquiry not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading enquiry data');
                }
            });
        });

        // Submit Add/Edit
        $('#enquiryForm').submit(function(e) {
            e.preventDefault();
            let id = $('#enquiry_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#enquiryForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#enquiryModal').modal('hide');
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
                        toastr.error(xhr.responseJSON?.message || 'Error saving enquiry');
                    }
                }
            });
        });

        // Delete Enquiry
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this enquiry?')) {
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
                    toastr.error(xhr.responseJSON?.message || 'Error deleting enquiry');
                }
            });
        });
    });
</script>
@endpush