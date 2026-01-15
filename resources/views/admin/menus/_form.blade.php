<!-- Menu Form Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"> Add Menu</h3>
            </div>
            <form id="menuForm" method="POST">
                @csrf
                <input type="hidden" id="menu_id" name="menu_id" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name">
                         <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="route">Route</label>
                        <input type="text" class="form-control" id="route" name="route" placeholder="e.g., admin.dashboard">
                         <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i id="iconPreview" class="fas fa-question"></i></span>
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g., fas fa-home">
                        </div>
                        <div class="invalid-feedback"></div>
                        <span class="help-block text-danger"></span>
                        <span class="help-block text-muted">Use Font Awesome icon classes</span>
                    </div>
                    <div class="form-group">
                        <label for="parent_id">Parent Menu</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">None (Top Level)</option>
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

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Menu</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function() {
    // Load menus and guards when modal opens
    $('#menuModal').on('show.bs.modal', function() {

console.log('Menu modal opened, loading parent menus and guards.');
    });
        loadParentMenus();
        loadGuards();

    // Load parent menus
    function loadParentMenus() {
        $.ajax({
            url: '{{ route("admin.menus.options") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">None (Top Level)</option>';
                    response.menus.forEach(function(menu) {
                        options += `<option value="${menu.id}">${menu.name}</option>`;
                        if (menu.children && menu.children.length > 0) {
                            menu.children.forEach(function(child) {
                                options += `<option value="${child.id}">-- ${child.name}</option>`;
                            });
                        }
                    });
                    $('#parent_id').html(options);
                }
            }
        });
    }

    // Load guards dynamically
    function loadGuards() {
        $.ajax({
            url: '{{ route("admin.menu-permissions.guards") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let guardOptions = '<option value="">Select Guard</option>';
                    response.guards.forEach(function(guard) {
                        guardOptions += `<option value="${guard}">${guard.charAt(0).toUpperCase() + guard.slice(1)}</option>`;
                    });
                    $('#guard_name').html(guardOptions);
                }
            },
            error: function() {
                console.error('Failed to load guards');
            }
        });
    }

    // Update icon preview
    $('#icon').on('input', function() {
        const iconClass = $(this).val() || 'fas fa-question';
        $('#iconPreview').attr('class', iconClass);
    });
});
</script>
@endpush
