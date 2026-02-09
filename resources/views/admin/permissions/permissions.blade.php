@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Permissions</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Permissions</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif

                                <button type="button" class="btn btn-primary" id="btn-create-permission">
                                    <i class="fas fa-plus"></i> NEW PERMISSION
                                </button>

                                <div class="table-responsive" style="margin-top: 15px;">
                                    <table class="table table-striped" id="permissions-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Display Name</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables will populate this -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
    <div class="clearfix"></div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="create-permission-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">New Permission</h4>
                </div>
                <form id="create-permission-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-resource">{{ __('Resource') }}</label>
                            <input id="create-resource" type="text" class="form-control" name="resource" 
                                   autocomplete="name" autofocus required>
                            <span class="text-danger" id="create-resource-error"></span>
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" checked name="permissions[]"
                                    value="create" id="create-perm-create">
                                <label for="create-perm-create"> Create</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" checked name="permissions[]"
                                    value="read" id="create-perm-read">
                                <label for="create-perm-read"> Read</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" checked name="permissions[]"
                                    value="update" id="create-perm-update">
                                <label for="create-perm-update"> Update</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" checked name="permissions[]"
                                    value="delete" id="create-perm-delete">
                                <label for="create-perm-delete"> Delete</label>
                            </div>
                            <span class="text-danger" id="create-permissions-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-create">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div class="modal fade" id="edit-permission-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Permission</h4>
                </div>
                <form id="edit-permission-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-permission-id" name="permission_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-name">{{ __('Name') }}</label>
                            <input id="edit-name" type="text" class="form-control" name="name" 
                                   maxlength="50" required>
                            <span class="text-danger" id="edit-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="edit-display-name">{{ __('Display Name') }}</label>
                            <input id="edit-display-name" type="text" class="form-control" name="display_name" 
                                   maxlength="100" required>
                            <span class="text-danger" id="edit-display-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="edit-description">{{ __('Description') }}</label>
                            <textarea id="edit-description" class="form-control" name="description" 
                                      rows="3" maxlength="255"></textarea>
                            <span class="text-danger" id="edit-description-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-edit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete-permission-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the permission: <strong id="permission-name"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-permission">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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

        var permissionsTable;
        var deletePermissionId = null;

        /**
         * Initialize Permissions DataTable
         */
        function initializePermissionsTable() {
            permissionsTable = $('#permissions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.permissions.index') }}",
                    type: 'GET'
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        width: '5%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '25%'
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        width: '25%'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        width: '30%',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    }
                ],
                order: [[0, 'asc']], // Sort by ID ascending
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
                language: {
                    search: "Search permissions:",
                    lengthMenu: "Show _MENU_ permissions per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                    infoEmpty: "No permissions available",
                    infoFiltered: "(filtered from _MAX_ total permissions)",
                    zeroRecords: "No matching permissions found",
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                responsive: true,
                autoWidth: false
            });
        }

        // Initialize DataTable on page load
        $(document).ready(function() {
            initializePermissionsTable();
        });

        /**
         * Clear form validation errors
         */
        function clearFormErrors(formId) {
            $('#' + formId + ' .text-danger').text('');
            $('#' + formId + ' .form-control').removeClass('is-invalid');
        }

        /**
         * Display form validation errors
         */
        function displayFormErrors(errors, formPrefix) {
            $.each(errors, function(field, messages) {
                $('#' + formPrefix + '-' + field + '-error').text(messages[0]);
                $('#' + formPrefix + '-' + field).addClass('is-invalid');
            });
        }

        /**
         * Handle Create Permission Button Click
         */
        $('#btn-create-permission').on('click', function() {
            clearFormErrors('create-permission-form');
            $('#create-permission-form')[0].reset();
            // Check all checkboxes by default
            $('#create-permission-form input[type="checkbox"]').prop('checked', true);
            $('#create-permission-modal').modal('show');
        });

        /**
         * Handle Create Permission Form Submit
         */
        $('#create-permission-form').on('submit', function(e) {
            e.preventDefault();
            clearFormErrors('create-permission-form');

            var submitButton = $('#btn-submit-create');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            $.ajax({
                url: "{{ route('admin.permissions.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#create-permission-modal').modal('hide');
                    submitButton.prop('disabled', false).html('Add');
                    
                    if (response.success) {
                        toastr.success(response.message || 'You have successfully added permissions');
                    } else {
                        toastr.success('You have successfully added permissions');
                    }
                    
                    permissionsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    submitButton.prop('disabled', false).html('Add');
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayFormErrors(errors, 'create');
                    } else {
                        var errorMessage = 'Failed to create permission';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                }
            });
        });

        /**
         * Handle Edit Permission Button Click
         */
        $(document).on('click', '.btn-group a[href*="permissions"]', function(e) {
            e.preventDefault();
            clearFormErrors('edit-permission-form');
            
            // Extract permission ID from URL
            var href = $(this).attr('href');
            var urlParts = href.split('/');
            var permissionId = urlParts[urlParts.length - 2]; // Get the ID before 'edit'
            
            console.log('Editing permission ID:', permissionId);
            console.log('Full URL:', href);
            
            $('#edit-permission-id').val(permissionId);

            // Load data first before showing modal
            var editButton = $('#btn-submit-edit');
            editButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

            $.ajax({
                url: "{{ url('admin/permissions') }}/" + permissionId + "/edit",
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success && response.permission) {
                        $('#edit-name').val(response.permission.name);
                        $('#edit-display-name').val(response.permission.display_name);
                        $('#edit-description').val(response.permission.description || '');
                        editButton.prop('disabled', false).html('Update');
                        
                        // Show modal after data is loaded
                        $('#edit-permission-modal').modal('show');
                    } else {
                        toastr.error('Failed to load permission data');
                        editButton.prop('disabled', false).html('Update');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading permission:', xhr);
                    toastr.error('Failed to load permission data');
                    editButton.prop('disabled', false).html('Update');
                }
            });
        });

        /**
         * Handle Edit Permission Form Submit
         */
        $('#edit-permission-form').on('submit', function(e) {
            e.preventDefault();
            clearFormErrors('edit-permission-form');

            var permissionId = $('#edit-permission-id').val();
            var submitButton = $('#btn-submit-edit');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

            $.ajax({
                url: "{{ url('admin/permissions') }}/" + permissionId,
                method: 'PUT',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#edit-permission-modal').modal('hide');
                    submitButton.prop('disabled', false).html('Update');
                    
                    if (response.success) {
                        toastr.success(response.message || 'Permission updated successfully');
                    } else {
                        toastr.success('Permission updated successfully');
                    }
                    
                    permissionsTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    submitButton.prop('disabled', false).html('Update');
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayFormErrors(errors, 'edit');
                    } else {
                        var errorMessage = 'Failed to update permission';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                }
            });
        });

        /**
         * Handle Delete Permission Button Click
         */
        $(document).on('click', '.btn-delete-permission', function() {
            deletePermissionId = $(this).data('id');
            var permissionName = $(this).data('name');
            
            $('#permission-name').text(permissionName);
            $('#delete-permission-modal').modal('show');
        });

        /**
         * Handle Confirm Delete
         */
        $(document).on('click', '#confirm-delete-permission', function() {
            if (!deletePermissionId) {
                toastr.error('No permission selected for deletion');
                return;
            }

            var deleteButton = $(this);
            deleteButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: "{{ url('admin/permissions') }}/" + deletePermissionId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#delete-permission-modal').modal('hide');
                    deleteButton.prop('disabled', false).html('Delete');
                    
                    if (response.success) {
                        toastr.success(response.message);
                        permissionsTable.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to delete permission');
                    }
                    
                    deletePermissionId = null;
                },
                error: function(xhr) {
                    deleteButton.prop('disabled', false).html('Delete');
                    
                    var errorMessage = 'Failed to delete permission';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    toastr.error(errorMessage);
                    
                    setTimeout(function() {
                        $('#delete-permission-modal').modal('hide');
                        deletePermissionId = null;
                    }, 1000);
                }
            });
        });

        /**
         * Reset modals when closed
         */
        $('#delete-permission-modal').on('hidden.bs.modal', function() {
            deletePermissionId = null;
            $('#confirm-delete-permission').prop('disabled', false).html('Delete');
            $(this).removeAttr('aria-hidden');
        });

        $('#create-permission-modal').on('hidden.bs.modal', function() {
            clearFormErrors('create-permission-form');
            $('#btn-submit-create').prop('disabled', false).html('Add');
            $(this).removeAttr('aria-hidden');
        });

        $('#edit-permission-modal').on('hidden.bs.modal', function() {
            clearFormErrors('edit-permission-form');
            $('#btn-submit-edit').prop('disabled', false).html('Update');
            $(this).removeAttr('aria-hidden');
        });

        /**
         * Handle modal shown events to fix aria-hidden
         */
        $('.modal').on('shown.bs.modal', function() {
            $(this).removeAttr('aria-hidden');
        });
    </script>
@endsection