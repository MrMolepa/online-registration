@extends('layouts.admin')

@section('title', 'Stock Types')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Stock Types</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addStockTypeBtn">
                                        <i class="fa fa-plus"></i> Add Stock Type
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-striped" id="stockTypeTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
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

@include('admin.stationery.stock-types._form')


@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table = $('#stockTypeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.stationery.stock-types.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                //{data: 'items_count', name: 'items_count', orderable: false, searchable: false},
                {data: 'status_badge', name: 'is_active'},
                //{data: 'created_at', name: 'created_at'},
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
        $('#addStockTypeBtn').click(function() {
            $('#stockTypeForm')[0].reset();
            $('#stock_type_id').val('');
            $('#stockTypeModalTitle').text('Add Stock Type');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#stockTypeForm').attr('action', '{{ route("admin.stationery.stock-types.store") }}');
            
            // Set default active status
            $('#is_active').prop('checked', true);
            
            $('#stockTypeModal').modal('show');
        });

        // Open modal for Edit // its almost 1 hour 30
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({ 
                url: url,
                type: 'GET',l,
                success: function(response) {
                    if (response.data) {
                        const stockType = response.data;
                        fillForm(stockType, '#stockTypeForm');
                        $('#stock_type_id').val(stockType.id);
                        $('#stockTypeModalTitle').text('Edit Stock Type');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#stockTypeForm').attr('action', response.url);
                        
                        $('#stockTypeModal').modal('show');
                    } else {
                        toastr.error('Stock type not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading stock type data');
                }
            });
        });

        // Open modal for View
        $(document).on('click', '.view-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        const stockType = response.data;
                        
                        // Populate view modal
                        $('#view_name').text(stockType.name || 'N/A');
                        $('#view_description').text(stockType.description || 'N/A');
                        $('#view_status').html(stockType.is_active 
                            ? '<span class="label label-success">Active</span>' 
                            : '<span class="label label-danger">Inactive</span>');
                        $('#view_items_count').text(stockType.stock_items ? stockType.stock_items.length : 0);
                        $('#view_created_at').text(new Date(stockType.created_at).toLocaleString());
                        $('#view_updated_at').text(new Date(stockType.updated_at).toLocaleString());
                        
                        // Populate stock items list
                        let itemsList = $('#view_stock_items');
                        itemsList.empty();
                        
                        if (stockType.stock_items && stockType.stock_items.length > 0) {
                            stockType.stock_items.forEach(function(item) {
                                itemsList.append('<li>' + item.name + ' (' + item.unit + ')</li>');
                            });
                        } else {
                            itemsList.append('<li class="text-muted">No stock items found</li>');
                        }
                        
                        $('#viewStockTypeModal').modal('show');
                    } else {
                        toastr.error('Stock type not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading stock type data');
                }
            });
        });

        // Submit Add/Edit
        $('#stockTypeForm').submit(function(e) {
            e.preventDefault();
            let id = $('#stock_type_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#stockTypeForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#stockTypeModal').modal('hide');
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
                        toastr.error(xhr.responseJSON?.message || 'Error saving stock type');
                    }
                }
            });
        });

        // Delete Stock Type
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this stock type?')) {
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
                    toastr.error(xhr.responseJSON?.message || 'Error deleting stock type');
                }
            });
        });
    });
</script>
@endpush