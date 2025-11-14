
<!-- Menu Permissions Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Menu Permissions</h3>
            </div>
            <div class="modal-body">
                <!-- Assign Permission Form -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Assign New Permission</h4>
                    </div>
                    <div class="panel-body">
                        <form id="permissionForm">
                            @csrf
                            <div class="form-group">
                                <label for="permission_id">Permission <span class="text-danger">*</span></label>
                                <select class="form-control" id="permission_id" name="permission_id">
                                    <option value="">Select Permission</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="role_id">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="role_id" name="role_id">
                                    <option value="">Select Role</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="guard_name">Guard Name <span class="text-danger">*</span></label>
                                <select class="form-control" id="guard_name" name="guard_name">
                                    <option value="">Select Guard</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-plus"></i> Assign
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>

                <!-- Assigned Permissions Table -->
                <div class="panel panel-default" style="width: 100%; margin: 0; padding: 0;">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Assigned Permissions 
                            <span class="badge" id="permissionCount">0</span>
                        </h4>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive" style="width: 100%;">
                            <table class="table table-bordered table-hover table-striped mb-0" id="permissionsTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%;">Permission</th>
                                        <th style="width: 30%;">Role</th>
                                        <th style="width: 25%;">Guard</th>
                                        <th style="width: 10%;">Actions</th>
                                     </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated by DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
 // Open Permissions Modal
        let currentMenuId = null;// store current menu id
        let permissionsTable = null;// DataTable instance for permissions
        
        $(document).on('click', '.permissions-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');// get the url to fetch permissions data
            let menuName = $(this).data('name');// get menu name for modal title
            
            $('#permissionModalTitle').text(`Permissions for ${menuName}`);// set modal title
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentMenuId = response.menu.id;// store current menu id
                        
                        // Populate dropdowns
                        let roleOptions = '<option value="">Select Role</option>';
                        $.each(response.roles, function(id, name) {
                            roleOptions += `<option value="${id}">${name}</option>`;
                        });
                        $('#role_id').html(roleOptions);// populate roles dropdown
                        
                        let permissionOptions = '<option value="">Select Permission</option>';// reset permissions dropdown
                        $.each(response.permissions, function(id, name) {
                            permissionOptions += `<option value="${id}">${name}</option>`;// build options
                        });
                        $('#permission_id').html(permissionOptions);// populate permissions dropdown
                        
                        // Initialize permissions table
                        if (permissionsTable) {
                            permissionsTable.destroy();// destroy existing table instance
                        }
                        
                        permissionsTable = $('#permissionsTable').DataTable({
                            data: response.assigned_permissions,
                            columns: [
                                {
                                    data: 'permission.name',
                                    defaultContent: '-'
                                },
                                {
                                    data: 'role.name',
                                    defaultContent: '-'
                                },
                                //{data: 'guard_name'},
                                {
                                    data: 'id',
                                    orderable: false,
                                    render: function(data) {
                                        return `<button class="btn btn-danger btn-sm delete-permission-btn" data-url="{{ url('admin/menu-permissions') }}/${data}">
                                            <i class="fa fa-trash"></i>
                                        </button>`;
                                    }
                                }
                            ],
                            drawCallback: function()// update permission count on draw
                            {
                                updatePermissionCount();// update the count badge
                            }
                        });
                        
                        $('#permissionModal').modal('show');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading permissions');
                }
            });
        });

        // Assign Permission
        $('#permissionForm').submit(function(e) {
            e.preventDefault();
            console.log($(this).serialize());
            
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: '{{ route("admin.menu-permissions.store") }}',
                type: 'POST',
                data: {
                    menu_id: currentMenuId,
                    permission_id: $('#permission_id').val(),// get selected permission
                    role_id: $('#role_id').val(),
                    guard_name: $('#permission_guard_name').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')// CSRF token
                },
                success: function(response) {
                    console.log('Permission assigned:', response);
                    if (response.success) {
                        $('#permissionForm')[0].reset();
                        $('#permission_guard_name').val('admin');
                        
                        if (permissionsTable) {
                            permissionsTable.row.add(response.permission).draw();// add new permission to table
                            updatePermissionCount();// update count badge
                        }
                        toastr.success(response.message);
                        console.log('Permission assigned successfully');
                    }
                },
                error: function(xhr) {
                    console.log('Error assigning permission:', xhr);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid');// mark field as invalid
                            $(`#${key}`).siblings('.invalid-feedback').text(value[0]);// show first error message
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Error assigning permission');
                    }
                }
            });
        });

        // Delete Permission
        $(document).on('click', '.delete-permission-btn', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to remove this permission?')) {
                return;
            }
            
            let url = $(this).data('url');
            let row = $(this).closest('tr');
            
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (permissionsTable) {
                            permissionsTable.row(row).remove().draw();
                            updatePermissionCount();
                        }
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error removing permission');
                }
            });
        });

        // Update permission count badge
        function updatePermissionCount() {
            if (permissionsTable) {
                const count = permissionsTable.rows().count();
                $('#permissionCount').text(count);
            }
        }


$(document).ready(function() {
    // Load menus and guards when modal opens
    $('#permissionModal').on('show.bs.modal', function() {
        let $modal = $(this);
        loadGuards($modal);
        console.log('Permission modal opened, loading guards.');
    });
   

    // Load guards dynamically
    function loadGuards($modal) {
        $.ajax({
            url: '{{ route("admin.menu-permissions.guards") }}',
            type: 'GET',
            success: function(response) {
                console.log('Guards loaded:', response);
                if (response.success) {
                    let guardOptions = '<option value="">Select Guard</option>';
                    response.guards.forEach(function(guard) {
                        guardOptions += `<option value="${guard}">${guard.charAt(0).toUpperCase() + guard.slice(1)}</option>`;
                    });
                    $modal.find('#guard_name').html(guardOptions);
                }
            },
            error: function() {
                console.error('Failed to load guards');
            }
        });
    }
});
</script>
@endpush
