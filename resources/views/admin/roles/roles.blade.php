@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">All Roles</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">All Roles</h3>
                            </div>
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <strong>Success! </strong> {{ session('success') }}
                                    </div>
                                @endif

                                <button type="button" class="btn btn-primary" id="btn-create-role">
                                    <i class="fas fa-plus"></i> NEW ROLE
                                </button>

                                <div class="table-responsive" style="margin-top: 15px;">
                                    <table class="table table-striped" id="roles-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Display Name</th>
                                                <th>Description</th>
                                                <th>Permissions</th>
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

    <!-- Create Role Modal -->
    <div class="modal fade" id="create-role-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">New Role</h4>
                </div>
                <form id="create-role-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-display-name">{{ __('Display Name') }}</label>
                            <input id="create-display-name" type="text" class="form-control" name="display_name"
                                maxlength="255" autofocus>
                            <span class="text-danger" id="create-display-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="create-name">{{ __('Name') }}</label>
                            <input id="create-name" type="text" class="form-control" name="name" maxlength="100">
                            <small class="form-text text-muted">Use lowercase letters, numbers, dashes, and underscores
                                only.</small>
                            <span class="text-danger" id="create-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="create-description">{{ __('Description') }}</label>
                            <textarea id="create-description" class="form-control" name="description" rows="4"
                                maxlength="255"></textarea>
                            <span class="text-danger" id="create-description-error"></span>
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

    <!-- Edit Role Modal -->
    <div class="modal fade" id="edit-role-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Role</h4>
                </div>
                <form id="edit-role-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-role-id" name="role_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-display-name">{{ __('Display Name') }}</label>
                            <input id="edit-display-name" type="text" class="form-control" name="display_name"
                                maxlength="255">
                            <span class="text-danger" id="edit-display-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="edit-name">{{ __('Name') }}</label>
                            <input id="edit-name" type="text" class="form-control" name="name" maxlength="100" disabled>
                            <small class="form-text text-muted">Name cannot be changed after creation.</small>
                            <span class="text-danger" id="edit-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="edit-description">{{ __('Description') }}</label>
                            <textarea id="edit-description" class="form-control" name="description" rows="4" maxlength="255"
                                required></textarea>
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
    <div class="modal fade" id="delete-role-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the role: <strong id="role-name"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-role">Delete</button>
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

        var deleteRoleId = null;
        var rolesTable;

        /**
         * Initialize Roles DataTable
         */
        function initializeRolesTable() {
            rolesTable = $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.roles.index') }}",
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
                        width: '20%'
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        width: '20%'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        width: '25%',
                        render: function (data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'permissions',
                        name: 'permissions',
                        orderable: false,
                        searchable: false,
                        width: '15%'
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
                    search: "Search roles:",
                    lengthMenu: "Show _MENU_ roles per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "No roles available",
                    infoFiltered: "(filtered from _MAX_ total roles)",
                    zeroRecords: "No matching roles found",
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                responsive: true,
                autoWidth: false
            });
        }

        // Prevent multiple event bindings
        $(document).ready(function () {
            console.log('Roles page initialized');
            initializeRolesTable();
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
            $.each(errors, function (field, messages) {
                var fieldName = field.replace('_', '-');
                $('#' + formPrefix + '-' + fieldName + '-error').text(messages[0]);
                $('#' + formPrefix + '-' + fieldName).addClass('is-invalid');
            });
        }

        /**
         * Handle Create Role Button Click
         */
        $('#btn-create-role').on('click', function () {
            clearFormErrors('create-role-form');
            $('#create-role-form')[0].reset();
            $('#create-role-modal').modal('show');
        });

        /**
         * Handle Create Role Form Submit
         */
        $('#create-role-form').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('create-role-form');

            var submitButton = $('#btn-submit-create');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            $.ajax({
                url: "{{ route('admin.roles.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    $('#create-role-modal').modal('hide');
                    submitButton.prop('disabled', false).html('Add');

                    if (response.success) {
                        toastr.success(response.message || 'Successfully created the role');
                    } else {
                        toastr.success('Successfully created the role');
                    }

                    // Reload DataTable
                    rolesTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitButton.prop('disabled', false).html('Add');

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayFormErrors(errors, 'create');
                    } else {
                        var errorMessage = 'Failed to create role';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                }
            });
        });

        /**
    * Handle Edit Role Button Click
    */
        $(document).on('click', '.btn-edit-role', function (e) {
            e.preventDefault();
            clearFormErrors('edit-role-form');

            var url = $(this).data('url');
            var roleId = $(this).data('id');

            $('#edit-role-id').val(roleId);

            var editButton = $('#btn-submit-edit');
            editButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    if (response.success && response.role) {
                        $('#edit-display-name').val(response.role.display_name);
                        $('#edit-name').val(response.role.name);
                        $('#edit-description').val(response.role.description || '');
                        editButton.prop('disabled', false).html('Update');
                        $('#edit-role-modal').modal('show');
                    } else {
                        toastr.error('Failed to load role data');
                        editButton.prop('disabled', false).html('Update');
                    }
                },
                error: function (xhr) {
                    toastr.error('Failed to load role data');
                    editButton.prop('disabled', false).html('Update');
                }
            });
        });

        /**
         * Handle Edit Role Form Submit
         */
        $('#edit-role-form').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('edit-role-form');

            var roleId = $('#edit-role-id').val();
            var submitButton = $('#btn-submit-edit');
            submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

            $.ajax({
                url: "{{ url('admin/roles') }}/" + roleId,
                method: 'PUT',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    $('#edit-role-modal').modal('hide');
                    submitButton.prop('disabled', false).html('Update');
                    toastr.success(response.message || 'Successfully updated the role');
                    rolesTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitButton.prop('disabled', false).html('Update');
                    if (xhr.status === 422) {
                        displayFormErrors(xhr.responseJSON.errors, 'edit');
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Failed to update role');
                    }
                }
            });
        });

        /**
         * Handle Delete Role Button Click
         */
        $(document).on('click', '.btn-delete-role', function (e) {
            e.preventDefault();
            e.stopPropagation();

            deleteRoleId = $(this).data('id');
            deleteRoleUrl = $(this).data('url');
            var roleName = $(this).data('name');

            if (!deleteRoleId) {
                toastr.error('Invalid role ID');
                return;
            }

            $('#role-name').text(roleName);
            $('#delete-role-modal').modal('show');
        });

        /**
         * Handle Confirm Delete
         */
        $(document).on('click', '#confirm-delete-role', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!deleteRoleId || !deleteRoleUrl) {
                toastr.error('No role selected for deletion');
                $('#delete-role-modal').modal('hide');
                return;
            }

            var deleteButton = $(this);
            if (deleteButton.prop('disabled')) return;

            deleteButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: deleteRoleUrl,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    $('#delete-role-modal').modal('hide');
                    deleteButton.prop('disabled', false).html('Delete');

                    if (response.success) {
                        toastr.success(response.message || 'Successfully deleted the role');
                        deleteRoleId = null;
                        deleteRoleUrl = null;
                        rolesTable.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to delete role');
                        deleteRoleId = null;
                        deleteRoleUrl = null;
                    }
                },
                error: function (xhr) {
                    deleteButton.prop('disabled', false).html('Delete');
                    toastr.error(xhr.responseJSON?.message || 'Failed to delete role');
                    setTimeout(function () {
                        $('#delete-role-modal').modal('hide');
                        deleteRoleId = null;
                        deleteRoleUrl = null;
                    }, 1000);
                }
            });
        });

        /**
         * Reset modals when closed
         */
        $('#delete-role-modal').on('hidden.bs.modal', function () {
            console.log('Delete modal closed');
            deleteRoleId = null;
            deleteRoleUrl = null;
            $('#confirm-delete-role').prop('disabled', false).html('Delete');
            $(this).removeAttr('aria-hidden');
        });

        $('#delete-role-modal').on('show.bs.modal', function () {
            console.log('Delete modal showing, role ID:', deleteRoleId);
            if (!deleteRoleId) {
                console.error('No role ID set when opening delete modal');
                $(this).modal('hide');
                toastr.error('No role selected for deletion');
                return false;
            }
        });

        $('#create-role-modal').on('hidden.bs.modal', function () {
            clearFormErrors('create-role-form');
            $('#btn-submit-create').prop('disabled', false).html('Add');
            $(this).removeAttr('aria-hidden');
        });

        $('#edit-role-modal').on('hidden.bs.modal', function () {
            clearFormErrors('edit-role-form');
            $('#btn-submit-edit').prop('disabled', false).html('Update');
            $(this).removeAttr('aria-hidden');
        });

        /**
         * Handle modal shown events to fix aria-hidden
         */
        $('.modal').on('shown.bs.modal', function () {
            $(this).removeAttr('aria-hidden');
        });
    </script>
@endsection