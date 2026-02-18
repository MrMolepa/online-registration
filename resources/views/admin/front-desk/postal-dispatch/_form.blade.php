<!-- Phone Call Log Form Modal -->
<div class="modal fade" id="postalDispatchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="postalDispatchModalTitle">Postal Dispatch</h3>
            </div>
            <form id="postalDispatchForm" method="POST">
                @csrf
                <input type="hidden" id="postalDispatch_id" name="postalDispatch_id" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="to">To</label>
                        <input type="text" class="form-control" id="to" name="to" placeholder="Enter recipient name">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="reference_no">Reference No<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="Enter reference number">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="address">Address<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter address">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="date">Date<span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="date" name="date" placeholder="Select date">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="from">From</label>
                        <input type="text" class="form-control" id="from" name="from" placeholder="Enter sender name">
                        <span class="help-block text-danger"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary resetform" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="savePostalDispatchBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>