<!-- Menu Form Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Add Menu</h3>
            </div>
            <form id="menuForm" method="POST">
                @csrf
                <input type="hidden" id="menu_id" name="menu_id" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="route">Route</label>
                        <input type="text" class="form-control" id="route" name="route" placeholder="e.g., admin.dashboard">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i id="iconPreview" class="fas fa-question"></i></span>
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g., fas fa-home">
                        </div>
                        <span class="help-block text-danger"></span>
                        <span class="help-block text-muted">Use Font Awesome icon classes</span>
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Menu</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">None (Top Level)</option>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="guard_name">Guard Name <span class="text-danger">*</span></label>
                        <select class="form-control" id="guard_name" name="guard_name">
                            <option value="">Select Guard</option>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <span class="help-block text-danger"></span>
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