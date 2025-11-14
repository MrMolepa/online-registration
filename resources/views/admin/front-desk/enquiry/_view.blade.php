<!-- Enquiry View Modal -->
<div class="modal fade" id="enquiryViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">
                    <i class="fa fa-question-circle"></i> Enquiry Details
                </h3>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Name:</strong></label>
                            <p class="form-control-static" id="view_name">-</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Email:</strong></label>
                            <p class="form-control-static" id="view_email">-</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Phone:</strong></label>
                            <p class="form-control-static" id="view_phone">-</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Enquiry Date:</strong></label>
                            <p class="form-control-static" id="view_enquiry_date">-</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><strong>Status:</strong></label>
                            <p class="form-control-static" id="view_status">-</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><strong>Description:</strong></label>
                            <div class="well well-sm" id="view_description" style="min-height: 80px; background-color: #f9f9f9;">
                                -
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Created At:</strong></label>
                            <p class="form-control-static" id="view_created_at">-</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><strong>Updated At:</strong></label>
                            <p class="form-control-static" id="view_updated_at">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editFromView">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control-static {
        min-height: 20px;
        padding-top: 7px;
        padding-bottom: 7px;
        margin-bottom: 0;
    }
    
    .well {
        margin-bottom: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    #enquiryViewModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    #enquiryViewModal .label {
        font-size: 100%;
        padding: .4em .8em;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentEnquiryId = null;
    let currentEditUrl = null;

    // Handle view button click
    $(document).on('click', '.view-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const enquiry = response.data;
                    currentEnquiryId = enquiry.id;
                    currentEditUrl = '{{ url("admin/front-desk/enquiry") }}/' + enquiry.id + '/edit';
                    
                    // Populate the view modal
                    $('#view_name').text(enquiry.name || '-');
                    $('#view_email').text(enquiry.email || '-');
                    $('#view_phone').text(enquiry.phone || '-');
                    $('#view_enquiry_date').text(formatDate(enquiry.enquiry_date) || '-');
                    
                    // Status with badge
                    const statusClass = enquiry.is_active ? 'label-success' : 'label-danger';
                    const statusText = enquiry.is_active ? 'Active' : 'Inactive';
                    $('#view_status').html('<span class="label ' + statusClass + '">' + statusText + '</span>');
                    
                    $('#view_description').text(enquiry.description || '-');
                    $('#view_created_at').text(formatDateTime(enquiry.created_at) || '-');
                    $('#view_updated_at').text(formatDateTime(enquiry.updated_at) || '-');
                    
                    $('#enquiryViewModal').modal('show');
                } else {
                    toastr.error('Enquiry not found');
                }
            },
            error: function(xhr) {
                toastr.error('Error loading enquiry details');
            }
        });
    });

    // Edit from view modal
    $('#editFromView').click(function() {
        $('#enquiryViewModal').modal('hide');
        
        // Slight delay to ensure view modal is closed
        setTimeout(function() {
            $.ajax({
                url: currentEditUrl,
                type: 'GET',
                success: function(response) {
                    if (response.data) {
                        const enquiry = response.data;
                        fillForm(enquiry, '#enquiryForm');
                        $('#enquiry_id').val(enquiry.id);
                        $('#enquiryModalTitle').text('Edit Enquiry');
                        $('.form-control').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').text('');
                        $('#enquiryForm').attr('action', response.url);
                        
                        // Re-initialize datepickers
                        $('.datepicker').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true
                        });
                        
                        $('#enquiryModal').modal('show');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading enquiry data');
                }
            });
        }, 300);
    });

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return null;
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: '2-digit' };
        return date.toLocaleDateString('en-US', options);
    }

    // Helper function to format datetime
    function formatDateTime(dateString) {
        if (!dateString) return null;
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Helper function to fill form fields
    function fillForm(data, formId) {
        const form = $(formId);
        $.each(data, function(key, value) {
            let field = $('[name="' + key + '"]');
            
            if (field.is(':checkbox')) {
                field.prop('checked', !!value);
            } else if (field.is(':radio')) {
                $('input[name="' + key + '"][value="' + value + '"]').prop('checked', true);
            } else {
                field.val(value);
            }
        });
    }
});
</script>
@endpush