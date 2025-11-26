@extends('layouts.admin')

@section('title', 'Stock Items')

@section('content')
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Stock Items</h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- PANEL NO CONTROLS -->
                    <div class="panel panel-headline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addStockItemBtn">
                                        <i class="fa fa-plus"></i> Add Stock Item
                                    </button>
                                    
                                    <div class="mt-3">
                                        <table class="table table-striped" id="stockItemTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Stock Type</th>
                                                    <th>Name</th>
                                                    <th>Stock Quantity</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
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

@include('admin.stationery.stock-items._form')
@include('admin.stationery.stock-items._view')

@endsection

@push('styles')
<style>
    .label { padding: .3em .6em; border-radius: .25em; }
    .label-success { background-color: #5cb85c; color: #fff; }
    .label-danger { background-color: #d9534f; color: #fff; }
    .mt-3 { margin-top: 20px; }
    .text-success { color: #5cb85c; }
    .text-danger { color: #d9534f; }
</style>
@endpush

@push('scripts')
<script>
    // TOASTER AND NOTIFICATION SETUP
    toastr.options = {
        closeButton: true,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-center",
        preventDuplicates: false,
        onclick: null,
        showDuration: "3000",
        hideDuration: "8000",
        timeOut: "10000",
        extendedTimeOut: "8000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Load stock types for dropdown
        function loadStockTypes() {
            $.ajax({
                url: "{{ route('admin.stationery.stock-types.options') }}",
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">Select Stock Type</option>';
                    response.data.forEach(function(stockType) {
                        options += '<option value="' + stockType.id + '">' + stockType.name + '</option>';
                    });
                    $('#stock_type_id').html(options);
                }
            });
        }

        let table = $('#stockItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.stationery.stock-items.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'stock_type_name', name: 'stockType.name'},
                {data: 'name', name: 'name'},
                {data: 'stock_display', name: 'stock_qty'},
                {data: 'status_badge', name: 'is_active'},
                {data: 'created_at', name: 'created_at'},
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
        $('#addStockItemBtn').click(function() {
            $('#stockItemForm')[0].reset();
            $('#stock_item_id').val('');
            $('#stockItemModalTitle').text('Add Stock Item');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#stockItemForm').attr('action', '{{ route("admin.stationery.stock-items.store") }}');
            
            // Set default active status
            $('#is_active').prop('checked', true);
            
            // Load stock types
            loadStockTypes();
            
            $('#stockItemModal').modal('show');
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
                        const stockItem = response.data;
                        
                        // Load stock types first
                        loadStockTypes();
                        
                        // Small delay to ensure dropdown is populated
                        setTimeout(function() {
                            fillForm(stockItem, '#stockItemForm');
                            $('#stock_item_id').val(stockItem.id);
                            $('#stockItemModalTitle').text('Edit Stock Item');
                            $('.form-control').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').text('');
                            $('#stockItemForm').attr('action', response.url);
                            
                            $('#stockItemModal').modal('show');
                        }, 200);
                    } else {
                        toastr.error('Stock item not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading stock item data');
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
                        const stockItem = response.data;
                        
                        // Populate view modal
                        $('#view_name').text(stockItem.name || 'N/A');
                        $('#view_stock_type').text(stockItem.stock_type ? stockItem.stock_type.name : 'N/A');
                        $('#view_unit').text(stockItem.unit || 'N/A');
                        $('#view_stock_qty').text(Number(stockItem.stock_qty).toFixed(2) + ' ' + stockItem.unit);;
                        $('#view_supplier_info').text(stockItem.supplier_info || 'N/A');
                        $('#view_status').html(stockItem.is_active 
                            ? '<span class="label label-success">Active</span>' 
                            : '<span class="label label-danger">Inactive</span>');
                        $('#view_created_at').text(new Date(stockItem.created_at).toLocaleString());
                        $('#view_updated_at').text(new Date(stockItem.updated_at).toLocaleString());
                        
                        // Populate linked components
                        let componentsList = $('#view_components');
                        componentsList.empty();
                        
                        if (stockItem.component_stocks && stockItem.component_stocks.length > 0) {
                            stockItem.component_stocks.forEach(function(cs) {
                                if (cs.component) {
                                    componentsList.append('<li>' + cs.component.name + ' (' + cs.component.code + ')</li>');
                                }
                            });
                        } else {
                            componentsList.append('<li class="text-muted">Not linked to any components</li>');
                        }
                        
                        $('#viewStockItemModal').modal('show');
                    } else {
                        toastr.error('Stock item not found');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading stock item data');
                }
            });
        });

        // Submit Add/Edit
        $('#stockItemForm').submit(function(e) {
            e.preventDefault();
            let id = $('#stock_item_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = $('#stockItemForm').attr('action');
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#stockItemModal').modal('hide');
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
                        toastr.error(xhr.responseJSON?.message || 'Error saving stock item');
                    }
                }
            });
        });

        // Delete Stock Item
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this stock item?')) {
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
                    toastr.error(xhr.responseJSON?.message || 'Error deleting stock item');
                }
            });
        });
    });
</script>
@endpush