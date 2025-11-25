<!-- Stock Type View Modal -->
<div class="modal fade" id="viewStockTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Stock Type Details</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Name</th>
                                    <td id="view_name"></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td id="view_description"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="view_status"></td>
                                </tr>
                                <tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Stock Items</h4>
                        <ul id="view_stock_items" class="list-group">
                            <!-- Stock items will be populated here -->
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
    #view_stock_items {
        padding-left: 20px;
    }
    #view_stock_items li {
        list-style-type: disc;
        margin-bottom: 5px;
    }
</style>