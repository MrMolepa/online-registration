<!-- Fun Walk Form Modal -->
<div class="modal fade" id="funWalkModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="funWalkModalTitle">Fun Walk</h3>
            </div>
            <form id="funWalkForm" method="POST">
                @csrf
                <input type="hidden" id="funWalk_id" name="funWalk_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter fun walk title">
                                <div class="invalid-feedback text-danger"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date<span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" id="date" name="date" placeholder="Select date">
                                <div class="invalid-feedback text-danger"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Enter location">
                        <div class="invalid-feedback text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="price">Price<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="Enter price">
                        <div class="invalid-feedback text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter fun walk description"></textarea>
                        <div class="invalid-feedback text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status<span class="text-danger">*</span></label><br>
                        <label class="radio-inline">
                            <input type="radio" id="status_active" name="status" value="active" checked> Active
                        </label>
                        <label class="radio-inline">
                            <input type="radio" id="status_inactive" name="status" value="inactive"> Inactive
                        </label>
                        <div class="invalid-feedback text-danger"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger resetform" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
