<!-- Test Calculator Modal -->
<div class="modal fade" id="calculatorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Test Allocation Calculator</h3>
            </div>
            <div class="modal-body">
                <p class="text-muted">Test how much stock will be allocated based on existing rules</p>

                <div class="form-group">
                    <label for="calc_stock_item_id">Select Stock Item<span class="text-danger">*</span></label>
                    <select class="form-control" id="calc_stock_item_id">
                        <option value="">Select Stock Item</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="calc_candidates">Number of Candidates<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="calc_candidates" placeholder="e.g., 50"
                        min="1">
                </div>

                <button type="button" class="btn btn-primary" id="calculateTestBtn">
                    <i class="fa fa-calculator"></i> Calculate
                </button>

                <!-- Result Section -->
                <div id="calc_result" style="display: none; margin-top: 20px;">
                    <div class="alert alert-success">
                        <h4><i class="fa fa-check-circle"></i> Calculated Quantity</h4>
                        <h2 id="calc_result_qty" class="text-center" style="font-size: 48px; font-weight: bold;">0</h2>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Calculation Breakdown</h4>
                        </div>
                        <div class="panel-body">
                            <div id="calc_breakdown"></div>
                        </div>
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
    #calc_breakdown ul {
        list-style-type: none;
        padding-left: 0;
    }

    #calc_breakdown li {
        padding: 8px;
        margin-bottom: 5px;
        background-color: #f5f5f5;
        border-left: 3px solid #337ab7;
    }

    #calc_breakdown li:last-child {
        background-color: #d9edf7;
        border-left-color: #31708f;
        font-weight: bold;
    }

    #calculatorModal .invalid-feedback {
        color: #f80d09ff !important;
        display: block;
    }

    ;
</style>
