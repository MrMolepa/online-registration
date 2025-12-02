<!-- Phone Call Log Form Modal -->
<div class="modal fade" id="visitorsBookModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="visitorsBookModalTitle">Visitors Book</h3>
            </div>
            <form id="visitorsBookForm" method="POST">
                @csrf
                <input type="hidden" id="visitorsBook_id" name="visitorsBook_id" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="visitor_name" name="visitor_name" placeholder="Enter visitor name">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="meeting_with">Meeting With<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="meeting_with" name="meeting_with" placeholder="Enter person to meet">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="number_of_person">Number of Persons<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="number_of_person" name="number_of_person" placeholder="Enter number of persons">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="contact_number">Contact Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter contact number">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="purpose">Purpose<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="purpose" name="purpose" placeholder="Enter purpose of visit">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="date_of_visit">Date of Visit<span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="date" name="date" placeholder="Select date of visit">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="in_time">In Time<span class="text-danger">*</span></label>
                        <input type="text" class="form-control timepicker" id="in_time" name="in_time" placeholder="Select in time">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="out_time">Out Time</label>
                        <input type="text" class="form-control timepicker" id="out_time" name="out_time" placeholder="Select out time">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary resetform" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="savevisitorsBookBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
