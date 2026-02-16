@extends('layouts.admin')

@section('title', 'Fun Walks')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Fun Walks</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addFunWalkBtn">
                                        <i class="fa fa-plus"></i> Fun Walk
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-striped" id="funWalkTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
                                                    <th>Date</th>
                                                    <th>Location</th>
                                                    <th>Price</th>
                                                    <th>Description</th>
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

@include('admin.fun-walk._form')

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

        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        let table = $('#funWalkTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fun-walk.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'date', name: 'date'},
                {data: 'location', name: 'location'},
                {data: 'price', name: 'price'},
                {data: 'description', name: 'description'},
                {data: 'status_badge', name: 'status'},
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
        $('#addFunWalkBtn').click(function() {
            $('#funWalkForm')[0].reset();
            $('#funWalk_id').val('');
            $('#funWalkModalTitle').text('Add Fun Walk');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('input[name="status"][value="active"]').prop('checked', true);
            $('#funWalkForm').attr('action', '{{ route("admin.fun-walk.store") }}');
            
            // Re-initialize datepickers after form reset
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            $('#funWalkModal').modal('show');
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
                        const funWalk = response.data;
                        fillForm(funWalk, '#funWalkForm');
                        $('#funWalk_id').val(funWalk.id);
                        $('#funWalkModalTitle').text('Edit Fun Walk');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#funWalkForm').attr('action', response.url);
                        
                        $('#funWalkModal').modal('show');
                    } else {
                        toastr.error('Fun Walk not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading fun walk data');
                }
            });
        });

        // Submit Add/Edit
        $('#funWalkForm').submit(function(e) {
            e.preventDefault();
            let id = $('#funWalk_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#funWalkForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#funWalkModal').modal('hide');
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
                        toastr.error(xhr.responseJSON?.message || 'Error saving fun walk');
                    }
                }        
            });
        });

        // Delete Fun Walk
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this fun walk?')) {
                return;
            }
            
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(response) {
                    table.ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting fun walk');
                }
            });
        });
    });

</script>
@endpush
