<!-- Stock Item Form Modal -->
<div class="modal fade" id="stockItemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="stockItemModalTitle">Stock Item</h3>
            </div>
            <form id="stockItemForm" method="POST">
                @csrf
                <input type="hidden" id="stock_item_id" name="stock_item_id" value="">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_type_id">Stock Type<span class="text-danger">*</span></label>
                                <select class="form-control" id="stock_type_id" name="stock_type_id">
                                    <option value="">Select Stock Type</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Item Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter item name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit">Unit<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., pack, box, piece, ream">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stock_qty">Stock Quantity<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="stock_qty" name="stock_qty" placeholder="0.00">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier_info">Supplier Info</label>
                                <input type="text" class="form-control" id="supplier_info" name="supplier_info" placeholder="Supplier details">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
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
                    <button type="submit" class="btn btn-primary">Save Stock Item</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>