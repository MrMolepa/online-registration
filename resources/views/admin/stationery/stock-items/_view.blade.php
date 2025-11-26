<!-- Stock Item View Modal -->
<div class="modal fade" id="viewStockItemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Stock Item Details</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Stock Type</th>
                                    <td id="view_stock_type"></td>
                                </tr>
                                <tr>
                                    <th>Item Name</th>
                                    <td id="view_name"></td>
                                </tr>
                                <tr>
                                    <th>Unit</th>
                                    <td id="view_unit"></td>
                                </tr>
                                <tr>
                                    <th>Stock Quantity</th>
                                    <td id="view_stock_qty"></td>
                                </tr>
                                <tr>
                                    <th>Supplier Info</th>
                                    <td id="view_supplier_info"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="view_status"></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td id="view_created_at"></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td id="view_updated_at"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Linked Components</h4>
                        <ul id="view_components" class="list-group">
                            <!-- Components will be populated here -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .mt-3 {
        margin-top: 20px;
    }
    #view_components {
        padding-left: 20px;
    }
    #view_components li {
        list-style-type: disc;
        margin-bottom: 5px;
    }
</style>