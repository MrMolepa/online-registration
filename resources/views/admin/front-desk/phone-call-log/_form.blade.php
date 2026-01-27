<!-- Phone Call Log Form Modal -->
<div class="modal fade" id="phoneCallLogModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="phoneCallLogModalTitle">Phone Call Log</h3>
            </div>
            <form id="phoneCallLogForm" method="POST">
                @csrf
                <input type="hidden" id="phoneCallLog_id" name="phoneCallLog_id" value="">

                < <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="form-group">
                                <label for="phone">Phone<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Enter phone number">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="form-group">
                                <label for="date">Date<span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                    placeholder="Select date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="call_type">Call Type<span class="text-danger">*</span></label><br>
                        <label class="radio-inline">
                            <input type="radio" id="call_type_incoming" name="call_type" value="Incoming" checked>
                            Incoming
                        </label>
                        <label class="radio-inline">
                            <input type="radio" id="call_type_outgoing" name="call_type" value="Outgoing"> Outgoing
                        </label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="next_follow_up_date">Next Follow Up Date</label>
                                <input type="text" class="form-control datepicker" id="next_follow_up_date"
                                    name="next_follow_up_date" placeholder="Select date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="call_duration">Call Duration</label>
                                <input type="text" class="form-control" id="call_duration" name="call_duration"
                                    placeholder="e.g., 5 minutes">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Enter call description"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="2"
                            placeholder="Additional notes"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save Phone Call Log</button>
            <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
        </div>
        </form>
    </div>
</div>
</div>