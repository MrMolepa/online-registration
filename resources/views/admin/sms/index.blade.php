@extends('layouts.admin')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">SMS</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- PANEL NO CONTROLS -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">SMS</h3>
                            </div>
                            <div class="panel-body">
                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active"><a href="#actions-tab"
                                                role="tab"data-toggle="tab">SMS</a></li>
                                        <li><a href="#action-order-tab" role="tab" data-toggle="tab">Contacts</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="actions-tab">
                                        <form id="smsForm">
                                            @csrf
                                            <!-- Recipients -->
                                            <div class="">
                                                <label for="recipientInput" class="form-label">Recipients</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="recipientInput"
                                                        placeholder="Enter phone number and press Enter">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        data-toggle="modal" data-target="#contactsModal">
                                                        <i class="bi bi-person-lines-fill"></i> Contacts
                                                    </button>



                                                </div>
                                                <div id="recipientTagsContainer" class="mt-2">
                                                    <div class="empty-tags-message py-1">No recipients added yet</div>
                                                </div>
                                                <input type="hidden" name="recipients" id="recipientsInput">
                                            </div>

                                            <!-- Add this near the message textarea -->
                                            <div class="mb-3">
                                                <label class="form-label">Templates</label>
                                                <div class="input-group">
                                                    <select class="form-control" id="templateSelect">
                                                        <option value="">Select a template</option>
                                                        <!-- Templates will be loaded via AJAX -->
                                                    </select>
                                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                                        id="loadTemplateBtn">
                                                        <i class="fas fa-arrow-circle-down"></i> Load
                                                    </button>




                                                      <button class="btn btn-sm btn-outline-success" type="button"
                                                        data-toggle="modal" data-target="#saveTemplateModal">
                                                        Save <i class="fas fa-save"></i>
                                                    </button>
                                                     <button class="btn btn-sm btn-outline-danger" type="button"
                                                        id="clearTemplateBtn">
                                                        <i class="fas fa-times-circle"></i> Clear
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Message -->
                                            <div class="mb-3">
                                                <label for="message" class="form-label">Message</label>
                                                <textarea class="form-control message-box" id="message" name="message" rows="5"
                                                    placeholder="Type your message here..."></textarea>
                                                <div class="character-count text-end mt-1">
                                                    <span id="charCount">0</span>/160 characters
                                                </div>
                                            </div>

                                            <!-- Options -->
                                            <div class="mb-4">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" id="unicodeCheck"
                                                        name="is_unicode">
                                                    <label class="form-check-label" for="unicodeCheck">Unicode (for special
                                                        characters)</label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="scheduleCheck">
                                                    <label class="form-check-label" for="scheduleCheck">Schedule
                                                        message</label>
                                                </div>
                                                <div class="mt-2" id="scheduleOptions" style="display: none;">
                                                    <input type="datetime-local" class="form-control" name="scheduled_at">
                                                </div>
                                            </div>

                                            <!-- Send Button -->
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-sm btn-primary ">
                                                    <i class="bi bi-send-fill me-2"></i> Send Message
                                                    <span id="loadingSpinner"
                                                        class="spinner-border spinner-border-sm ms-2"></span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="action-order-tab">
                                        <div class="table-responsive">

                                        </div>

                                    </div>

                                    <!-- END TABBED CONTENT -->
                                </div>




                            </div>

                        </div>
                        <!-- END PANEL NO CONTROLS -->
                    </div>

                </div>


            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->

    <!-- Contacts Modal -->
    <div class="modal fade" id="contactsModal" tabindex="-1" aria-labelledby="contactsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">



                    <h5 class="modal-title" id="contactsModalLabel">Select Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="contactSearch" placeholder="Search contacts...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="selectAllContacts"></th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Group</th>
                                </tr>
                            </thead>
                            <tbody id="contactsTableBody">
                                <!-- Contacts will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addSelectedContacts">Add Selected</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal for saving templates -->
    <div class="modal fade" id="saveTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                        <button type="button" class="close resetform" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">New Template</h3>
                    </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name">
                    </div>
                      <div class="mb-3">
                        <label for="name" class="form-label">Description</label>
                        <input type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="templateContent" class="form-label">Content</label>
                        <textarea class="form-control" id="content" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTemplateBtn">Save Template</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body">
                SMS sent successfully!
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            const recipientInput = $('#recipientInput');
            const recipientTagsContainer = $('#recipientTagsContainer');
            const emptyTagsMessage = $('.empty-tags-message');
            const smsForm = $('#smsForm');
            const loadingSpinner = $('#loadingSpinner');
            const successToast = new bootstrap.Toast($('#successToast'));

            // Store recipients
            let recipients = [];

            // Character count
            $('#message').on('input', function() {
                $('#charCount').text($(this).val().length);
            });

            // Schedule options toggle
            $('#scheduleCheck').change(function() {
                $('#scheduleOptions').toggle(this.checked);
            });

            // Add recipient when pressing Enter
            recipientInput.keydown(function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const phoneNumber = $(this).val().trim();
                    if (phoneNumber && !recipients.includes(phoneNumber)) {
                        addRecipient(phoneNumber);
                        $(this).val('');
                    }
                }
            });

            // Load contacts via AJAX when modal opens
            $('#contactsModal').on('show.bs.modal', function() {
                alert('ok');
                $.get('/sms/contacts', function(data) {
                    const tableBody = $('#contactsTableBody');
                    tableBody.empty();
                    data.forEach(contact => {
                        tableBody.append(`
                            <tr>
                                <td><input type="checkbox" class="form-check-input contact-checkbox" data-phone="${contact.phone}"></td>
                                <td>${contact.name}</td>
                                <td>${contact.phone}</td>
                                <td>${contact.group}</td>
                            </tr>
                        `);
                    });
                });
            });

            // Search contacts
            $('#contactSearch').keyup(function() {
                const searchText = $(this).val().toLowerCase();
                $('#contactsTableBody tr').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.includes(searchText));
                });
            });

            // Select all contacts
            $('#selectAllContacts').change(function() {
                $('.contact-checkbox').prop('checked', this.checked);
            });

            // Add selected contacts
            $('#addSelectedContacts').click(function() {
                $('.contact-checkbox:checked').each(function() {
                    const phoneNumber = $(this).data('phone');
                    if (!recipients.includes(phoneNumber)) {
                        addRecipient(phoneNumber);
                    }
                });
                $('#contactsModal').modal('hide');
            });

            // Add a new recipient
            function addRecipient(phoneNumber) {
                recipients.push(phoneNumber);
                updateRecipientTags();
            }

            // Remove a recipient
            function removeRecipient(phoneNumber) {
                recipients = recipients.filter(r => r !== phoneNumber);
                updateRecipientTags();
            }

            // Update the visual tags
            function updateRecipientTags() {
                recipientTagsContainer.empty();
                $('#recipientsInput').val(JSON.stringify(recipients));

                if (recipients.length === 0) {
                    recipientTagsContainer.append(emptyTagsMessage);
                    return;
                }

                recipients.forEach(phoneNumber => {
                    recipientTagsContainer.append(`
                        <div class="recipient-tag">
                            ${phoneNumber}
                            <span class="recipient-tag-remove" data-phone="${phoneNumber}">
                                <i class="bi bi-x"></i>
                            </span>
                        </div>
                    `);
                });

                // Add event listeners to remove buttons
                $('.recipient-tag-remove').click(function() {
                    removeRecipient($(this).data('phone'));
                });
            }

            // Form submission
            smsForm.submit(function(e) {
                e.preventDefault();

                if (recipients.length === 0) {
                    alert('Please add at least one recipient');
                    return;
                }

                const message = $('#message').val().trim();
                if (!message) {
                    alert('Please enter a message');
                    return;
                }

                loadingSpinner.show();

                $.ajax({
                    url: '/sms/send',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Reset form
                            recipients = [];
                            updateRecipientTags();
                            $('#message').val('');
                            $('#charCount').text('0');
                            $('#unicodeCheck').prop('checked', false);
                            $('#scheduleCheck').prop('checked', false);
                            $('#scheduleOptions').hide();

                            // Show success message
                            successToast.show();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            for (const field in errors) {
                                errorMessages.push(errors[field].join('\n'));
                            }

                            alert('Validation errors:\n' + errorMessages.join('\n'));
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        loadingSpinner.hide();
                    }
                });
            });


            // Load templates when page loads
            function loadTemplates() {
                $.get('/sms/templates', function(data) {
                    const templateSelect = $('#templateSelect');
                    templateSelect.empty().append('<option value="">Select a template</option>');

                    data.forEach(template => {
                        templateSelect.append(
                            `<option value="${template.id}">${template.name}</option>`);
                    });
                });
            }

            // Load selected template
            $('#loadTemplateBtn').click(function() {
                const templateId = $('#templateSelect').val();
                if (templateId) {
                    $.get(`/sms/templates/${templateId}`, function(template) {
                        $('#message').val(template.content);
                        $('#charCount').text(template.content.length);
                    });
                }
            });

            // Save template
            $('#saveTemplateBtn').click(function() {
                const name = $('#templateName').val().trim();
                const content = $('#templateContent').val().trim();

                if (!name || !content) {
                    alert('Please provide both name and content');
                    return;
                }

                $.post({{route('admin.sms.saveTemplate')}}, {
                    name: name,
                    content: content,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(response) {
                    $('#saveTemplateModal').modal('hide');
                    loadTemplates(); // Refresh template list
                    $('#templateName').val('');
                    $('#templateContent').val('');
                }).fail(function(xhr) {
                    alert('Error saving template: ' + xhr.responseJSON.message);
                });
            });

            // Clear message
            $('#clearTemplateBtn').click(function() {
                $('#message').val('');
                $('#charCount').text('0');
            });

            // Pre-fill template content when saving
            $('#saveTemplateModal').on('show.bs.modal', function() {
                $('#templateContent').val($('#message').val());
            });

        });
    </script>
@endsection
