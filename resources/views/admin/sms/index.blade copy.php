<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Sender</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .message-box {
            min-height: 150px;
            resize: vertical;
        }
        .recipient-tag {
            display: inline-flex;
            align-items: center;
            background-color: #e3f2fd;
            color: #1976d2;
            border-radius: 16px;
            padding: 4px 12px;
            margin: 0 5px 5px 0;
            font-size: 0.85rem;
        }
        .recipient-tag-remove {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #bbdefb;
            color: #0d47a1;
            cursor: pointer;
            font-size: 0.7rem;
        }
        .recipient-tag-remove:hover {
            background-color: #90caf9;
        }
        .character-count {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .sms-card {
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        #recipientTagsContainer {
            min-height: 40px;
            border: 1px dashed #dee2e6;
            border-radius: 4px;
            padding: 5px;
            margin-top: 5px;
        }
        .empty-tags-message {
            color: #6c757d;
            font-style: italic;
            font-size: 0.9rem;
        }
        #loadingSpinner {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="sms-card card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-chat-text me-2"></i> Send SMS</h5>
            </div>
            <div class="card-body">
                <form id="smsForm">
                    @csrf
                    <!-- Recipients -->
                    <div class="mb-3">
                        <label for="recipientInput" class="form-label">Recipients</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="recipientInput" placeholder="Enter phone number and press Enter">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#contactsModal">
                                <i class="bi bi-person-lines-fill"></i> Contacts
                            </button>
                        </div>
                        <div id="recipientTagsContainer" class="mt-2">
                            <div class="empty-tags-message py-1">No recipients added yet</div>
                        </div>
                        <input type="hidden" name="recipients" id="recipientsInput">
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control message-box" id="message" name="message" rows="5" placeholder="Type your message here..."></textarea>
                        <div class="character-count text-end mt-1">
                            <span id="charCount">0</span>/160 characters
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="unicodeCheck" name="is_unicode">
                            <label class="form-check-label" for="unicodeCheck">Unicode (for special characters)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="scheduleCheck">
                            <label class="form-check-label" for="scheduleCheck">Schedule message</label>
                        </div>
                        <div class="mt-2" id="scheduleOptions" style="display: none;">
                            <input type="datetime-local" class="form-control" name="scheduled_at">
                        </div>
                    </div>

                    <!-- Send Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send-fill me-2"></i> Send Message
                            <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-2"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                SMS sent successfully!
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        });
    </script>
</body>
</html>
