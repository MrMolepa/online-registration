<!-- Postal Receive Form Modal -->
<div class="modal fade" id="postalReceiveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="postalReceiveModalTitle">Postal Receive</h3>
            </div>
            <form id="postalReceiveForm" method="POST">
                @csrf
                <input type="hidden" id="postalReceive_id" name="postalReceive_id" value="">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="package_name">Package Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="package_name" name="package_name" placeholder="Enter package name">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="from_title">From<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="from_title" name="from_title" placeholder="Enter sender name/title">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="to_title">To<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="to_title" name="to_title" placeholder="Enter recipient name/title">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="reference_number">Reference Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Enter reference number">
                        <div class="invalid-feedback"></div>
                        <span class="help-block text-muted">This must be unique</span>
                    </div>

                    <div class="form-group">
                        <label for="date_received">Date Received<span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="date_received" name="date_received" placeholder="Select date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Postal Receive</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>