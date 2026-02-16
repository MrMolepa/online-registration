<div class="modal fade" id="editRegistrationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Registration</h4>
            </div>
            <form id="editRegistrationForm">
                @csrf
                <input type="hidden" id="edit_registration_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fun_walk_id">Fun Walk <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_fun_walk_id" name="fun_walk_id">
                                    <option value="">Select Fun Walk</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ticket_number">Ticket Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_ticket_number" name="ticket_number">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" maxlength="8" required>
                                <small class="form-text text-muted">8-digit phone number</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" id="edit_date_of_birth" name="date_of_birth" required readonly>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_gender">Gender <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_qr_path">QR Code Path</label>
                        <input type="text" class="form-control" id="edit_qr_path" name="qr_path">
                        <small class="form-text text-muted">Optional - Path to QR code image</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>