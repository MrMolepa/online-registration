{{-- Subject Groups Content (Without Layout) --}}
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-group-modal">
    <i class="fa fa-plus"></i> Subject Group
</button>

<div class="clearfix" style="margin-bottom: 20px;"></div>

<div class="table-responsive">
    <table class="table table-striped" id="subject_groups_table">
        <thead>
            <tr>
                <th>Group Code</th>
                <th>Group Name</th>
                <th>Level</th>
                <th>Subjects</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

@include('admin.subject-groups._form')

@push('scripts')
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: "5000"
        };

        var subjectGroupsTable;

        $(document).ready(function () {
            // Initialize Select2 for Add Modal
            $('#subjects_select').select2({
                placeholder: 'Select subjects',
                allowClear: true,
                dropdownParent: $('#add-group-modal')
            });

            // Initialize Select2 for Edit Modal
            $('#edit_subjects_select').select2({
                placeholder: 'Select subjects',
                allowClear: true,
                dropdownParent: $('#edit-group-modal')
            });

            initializeGroupsDataTable();

            // Auto-filter on dropdown change
            $('#filter_level').on('change', function () {
                subjectGroupsTable.ajax.reload();
            });

            // Reset Select2 when add modal is closed
            $('#add-group-modal').on('hidden.bs.modal', function () {
                $('#subjects_select').val(null).trigger('change');
                $('#addGroupForm')[0].reset();
                clearErrorMessages('#addGroupForm');
            });

            // Reset Select2 when edit modal is closed
            $('#edit-group-modal').on('hidden.bs.modal', function () {
                $('#edit_subjects_select').val(null).trigger('change');
                $('#editGroupForm')[0].reset();
                clearErrorMessages('#editGroupForm');
            });

            // Add group
            $('#addGroupForm').on('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                clearErrorMessages('#addGroupForm');

                // Disable submit button to prevent double submission
                var $submitBtn = $('#save-group');
                $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.subject-groups.store') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.errors) {
                            printErrorMsg('#addGroupForm', response.errors);
                            $submitBtn.prop('disabled', false).html(
                                '<i class="fa fa-save"></i> Save');
                        } else {
                            $('#addGroupForm')[0].reset();
                            $('#subjects_select').val(null).trigger('change');
                            $('#add-group-modal').modal('hide');
                            toastr.success(response.success ||
                                'Subject group added successfully');
                            subjectGroupsTable.ajax.reload();
                            $submitBtn.prop('disabled', false).html(
                                '<i class="fa fa-save"></i> Save');
                        }
                    },
                    error: function (xhr) {
                        $submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save"></i> Save');

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            printErrorMsg('#addGroupForm', errors);
                            toastr.error('Please fix the validation errors');
                        } else if (xhr.status === 419) {
                            toastr.error('Session expired. Please refresh the page.');
                        } else {
                            toastr.error('An error occurred. Please try again.');
                            console.error('Error:', xhr);
                        }
                    }
                });
            });

            // Edit button - Open modal with data
            $(document).on('click', '.editBtn', function () {
                var groupId = $(this).data('id');
                var url = $(this).data('url');

                console.log('Edit clicked - ID:', groupId, 'URL:', url); // Debug line

                // Fetch group data
                $.ajax({
                    url: url,
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log('Data received:', data); // Debug line

                        // Populate form fields
                        $('#edit_group_id').val(data.id);
                        $('#edit_group_code').val(data.group_code);
                        $('#edit_group_name').val(data.group_name);
                        $('#edit_level_id').val(data.level_id);
                        $('#edit_is_active').prop('checked', data.is_active == 1);

                        // Set subjects in Select2
                        if (data.subjects && data.subjects.length > 0) {
                            $('#edit_subjects_select').val(data.subjects).trigger('change');
                        } else {
                            $('#edit_subjects_select').val(null).trigger('change');
                        }

                        // Store update URL in form
                        $('#editGroupForm').data('update-url', url);

                        // Show modal
                        $('#edit-group-modal').modal('show');
                    },
                    error: function (xhr) {
                        console.error('AJAX Error:', xhr); // Debug line
                        toastr.error('Failed to load group data');
                    }
                });
            });
            // Update group
            $('#editGroupForm').on('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                clearErrorMessages('#editGroupForm');

                // Disable submit button
                var $submitBtn = $('#update-group');
                $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                var url = $(this).data('update-url');
                var formData = $(this).serialize();

                $.ajax({
                    url: url,
                    method: "PUT",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.errors) {
                            printErrorMsg('#editGroupForm', response.errors);
                            $submitBtn.prop('disabled', false).html(
                                '<i class="fa fa-save"></i> Update');
                        } else {
                            $('#editGroupForm')[0].reset();
                            $('#edit_subjects_select').val(null).trigger('change');
                            $('#edit-group-modal').modal('hide');
                            toastr.success(response.success || 'Group updated successfully');
                            subjectGroupsTable.ajax.reload();
                            $submitBtn.prop('disabled', false).html(
                                '<i class="fa fa-save"></i> Update');
                        }
                    },
                    error: function (xhr) {
                        $submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save"></i> Update');

                        if (xhr.status === 422) {
                            printErrorMsg('#editGroupForm', xhr.responseJSON.errors);
                            toastr.error('Please fix the validation errors');
                        } else {
                            toastr.error('An error occurred while updating.');
                            console.error('Error:', xhr);
                        }
                    }
                });
            });

            // Delete button
            $(document).on('click', '#subject_groups_table .deleteBtn', function () {
                if (!confirm('Are you sure you want to delete this group?')) return;

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        toastr.success(data.success || 'Group deleted successfully');
                        subjectGroupsTable.ajax.reload();
                    },
                    error: function (xhr) {
                        toastr.error('An error occurred while deleting.');
                        console.error('Error:', xhr);
                    }
                });
            });
        });

        function initializeGroupsDataTable() {
            if ($.fn.DataTable.isDataTable('#subject_groups_table')) {
                $('#subject_groups_table').DataTable().destroy();
            }

            subjectGroupsTable = $('#subject_groups_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.subject-groups.index') }}",
                    data: function (d) {
                        d.level_id = $('#filter_level').val();
                    }
                },
                columns: [{
                    data: 'group_code',
                    name: 'group_code'
                },
                {
                    data: 'group_name',
                    name: 'group_name'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: 'subjects',
                    name: 'subjects',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ]
            });
        }

        function printErrorMsg(parent, errors) {
            $(parent + ' .help-block').text('').removeClass('text-danger');
            $.each(errors, function (key, value) {
                var errorMsg = Array.isArray(value) ? value[0] : value;
                $(parent + ' [name="' + key + '"]').next('.help-block').text(errorMsg).addClass('text-danger');

                // Also handle array inputs like subjects[]
                if (key.indexOf('.') !== -1) {
                    var fieldName = key.split('.')[0] + '[]';
                    $(parent + ' [name="' + fieldName + '"]').closest('.form-group').find('.help-block').text(
                        errorMsg).addClass('text-danger');
                }
            });
        }

        function clearErrorMessages(formSelector) {
            $(formSelector + ' .help-block').text('').removeClass('text-danger');
            $(formSelector + ' .form-group').removeClass('has-error');
        }
</script>
@endpush