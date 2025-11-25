<!-- Stock Type Form Modal -->
<div class="modal fade" id="stockTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="stockTypeModalTitle">Stock Type</h3>
            </div>
            <form id="stockTypeForm" method="POST">
                @csrf
                <input type="hidden" id="stock_type_id" name="stock_type_id" value="">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter stock type name (e.g., Question Paper Packs)">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter description (optional)"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Stock Type</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>