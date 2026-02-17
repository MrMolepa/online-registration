<div class="modal fade" id="viewRegistrationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Registration Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Personal Information</strong></h5>
                        <table class="table table-condensed">
                            <tr>
                                <th width="40%">Ticket Number:</th>
                                <td id="view_ticket_number">-</td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td id="view_full_name">-</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="view_email">-</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td id="view_phone">-</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td id="view_gender">-</td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td id="view_dob">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><strong>Event Information</strong></h5>
                        <table class="table table-condensed">
                            <tr>
                                <th width="40%">Fun Walk:</th>
                                <td id="view_fun_walk">-</td>
                            </tr>
                            <tr>
                                <th>Registered At:</th>
                                <td id="view_registered_at">-</td>
                            </tr>
                        </table>
                        
                        <h5 class="mt-3"><strong>QR Code</strong></h5>
                        <div id="view_qr_code" class="text-center">
                            <p class="text-muted">No QR Code available</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Payment History</strong></h5>
                        <div id="view_payments">
                            <p class="text-muted">No payments recorded</p>
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