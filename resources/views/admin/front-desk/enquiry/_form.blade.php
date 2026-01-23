<!-- Enquiry Form Modal -->
<div class="modal fade" id="enquiryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="enquiryModalTitle">Enquiry</h3>
            </div>
            <form id="enquiryForm" method="POST">
                @csrf
                <input type="hidden" id="enquiry_id" name="enquiry_id" value="">

                <div class="modal-body">
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter email address">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Enter phone number">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="enquiry_date">Enquiry Date<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="enquiry_date"
                                        name="enquiry_date" placeholder="Select date">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                placeholder="Enter enquiry description"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Enquiry</button>
                        <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                    </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize datepicker when modal opens
            $('#enquiryModal').on('show.bs.modal', function () {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
            });
        });
    </script>
@endpush