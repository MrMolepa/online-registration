{{-- Create Validation Rule Modal --}}
<div class="modal fade" id="createRuleModal" tabindex="-1" role="dialog" aria-labelledby="createRuleModalLabel">
    <div class="modal-dialog modal-md" role="document" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="createRuleModalLabel">Create Validation Rule</h4>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div id="createRuleFormContainer">
                    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Rule Name *</label>
                                <input type="text" name="rule_name" id="rule_name" class="form-control">
                                <span class="help-block text-danger" id="error_rule_name"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Level *</label>
                                <select name="level_id" id="level_id" class="form-control">
                                    <option value="">Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block text-danger" id="error_level_id"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Registration Type *</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="1">Full Registration</option>
                                    <option value="2">Partial Registration</option>
                                    <option value="3">Private Registration</option>
                                </select>
                                <span class="help-block text-danger" id="error_type"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked> Active
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rule Builder Section -->
                    <h4>Rule Configuration</h4>
                    <p class="text-muted">Configure validation rules for subject selection</p>

                    <!-- Subject Count Constraints -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Subject Count Constraints</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Minimum Subjects</label>
                                        <input type="number" id="min_subjects" class="form-control" min="0"
                                            placeholder="e.g., 5">
                                        <small class="text-muted">Leave blank for no minimum</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Maximum Subjects</label>
                                        <input type="number" id="max_subjects" class="form-control" min="0"
                                            placeholder="e.g., 10">
                                        <small class="text-muted">Leave blank for no maximum</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Groups Section -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Available Subject Groups</h4>
                        </div>
                        <div class="panel-body">
                            <div id="available-groups-container">
                                <p class="text-muted">Select a level to load available subject groups</p>
                            </div>
                        </div>
                    </div>

                    <!-- Required Groups -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Required Groups</h4>
                        </div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-sm btn-success" id="add-required-group">
                                <i class="fa fa-plus"></i> Add Required Group
                            </button>
                            <div id="required-groups-container" style="margin-top: 15px;">
                                <!-- Required groups will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Forbidden Groups -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Forbidden Groups</h4>
                        </div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-sm btn-danger" id="add-forbidden-group">
                                <i class="fa fa-plus"></i> Add Forbidden Group
                            </button>
                            <div id="forbidden-groups-container" style="margin-top: 15px;">
                                <!-- Forbidden groups will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Group Constraints -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Advanced Constraints</h4>
                        </div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-sm btn-primary" id="add-constraint">
                                <i class="fa fa-plus"></i> Add Constraint
                            </button>
                            <div id="constraints-container" style="margin-top: 15px;">
                                <!-- Constraints will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- JSON Preview -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                JSON Preview
                                <button type="button" class="btn btn-xs btn-default pull-right" id="toggle-json">
                                    <i class="fa fa-eye"></i> Show/Hide
                                </button>
                            </h4>
                        </div>
                        <div class="panel-body" id="json-preview-container" style="display: none;">
                            <pre id="json-preview" style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></pre>
                        </div>
                    </div>

                    <input type="hidden" name="rules" id="rules-json">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" id="saveRuleBtn" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var availableGroups = [];
    var requiredGroupsCounter = 0;
    var forbiddenGroupsCounter = 0;
    var constraintsCounter = 0;

    $(document).ready(function() {
        console.log('Modal script loaded');
        
        // Reset form when modal is closed
        $('#createRuleModal').on('hidden.bs.modal', function() {
            resetCreateForm();
        });

        // Load groups when level changes - Direct binding
        $('#level_id').on('change', function() {
            var selectedLevel = $(this).val();
            console.log('Level changed to:', selectedLevel);
            if (selectedLevel) {
                loadAvailableGroups(selectedLevel);
            } else {
                $('#available-groups-container').html(
                    '<p class="text-muted">Select a level to load available subject groups</p>');
                availableGroups = [];
            }
        });

        // Toggle JSON preview
        $('#toggle-json').on('click', function() {
            $('#json-preview-container').slideToggle();
            updateJsonPreview();
        });

        // Add required group
        $('#add-required-group').on('click', function() {
            addRequiredGroup();
        });

        // Add forbidden group
        $('#add-forbidden-group').on('click', function() {
            addForbiddenGroup();
        });

        // Add constraint
        $('#add-constraint').on('click', function() {
            addConstraint();
        });

        // Update JSON on any change
        $(document).on('change input', '#min_subjects, #max_subjects, .rule-input', function() {
            updateJsonPreview();
        });

        // Form submission
        $('#saveRuleBtn').on('click', function(e) {
            e.preventDefault();
            saveRule();
        });
    });

    function resetCreateForm() {
        $('#rule_name').val('');
        $('#level_id').val('');
        $('#type').val('');
        $('#is_active').prop('checked', true);
        $('#min_subjects').val('');
        $('#max_subjects').val('');
        
        $('.help-block').text('');
        $('#available-groups-container').html(
            '<p class="text-muted">Select a level to load available subject groups</p>');
        $('#required-groups-container').empty();
        $('#forbidden-groups-container').empty();
        $('#constraints-container').empty();
        $('#json-preview-container').hide();
        $('#json-preview').text('');
        
        availableGroups = [];
        requiredGroupsCounter = 0;
        forbiddenGroupsCounter = 0;
        constraintsCounter = 0;
    }

    function loadAvailableGroups(levelId) {
        console.log('loadAvailableGroups called with levelId:', levelId);
        
        if (!levelId) {
            console.warn('No levelId provided');
            return;
        }

        // Show loading state
        $('#available-groups-container').html(
            '<p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading groups...</p>');

        var url = "{{ route('admin.subject-group-rules.getGroups') }}";
        console.log('AJAX URL:', url);
        console.log('Sending level_id:', levelId);

        $.ajax({
            url: url,
            method: "GET",
            data: {
                level_id: levelId
            },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Success:', response);
                
                if (response.success && response.groups) {
                    availableGroups = response.groups;
                    console.log('Groups loaded:', availableGroups.length);
                    displayAvailableGroups();
                } else {
                    console.warn('No groups in response');
                    availableGroups = [];
                    displayAvailableGroups();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                toastr.error('Error loading subject groups: ' + (xhr.responseJSON?.message || error));
                $('#available-groups-container').html(
                    '<div class="alert alert-danger">Error loading subject groups. Please try again. Status: ' + xhr.status + '</div>');
                availableGroups = [];
            }
        });
    }

    function displayAvailableGroups() {
        console.log('displayAvailableGroups called with', availableGroups.length, 'groups');
        
        if (!availableGroups || availableGroups.length === 0) {
            $('#available-groups-container').html(
                '<div class="alert alert-warning">No subject groups found for this level. ' +
                '<a href="{{ route('admin.subject-groups.index') }}" target="_blank">Create subject groups first</a>.</div>'
            );
            return;
        }

        var html = '<table class="table table-sm table-bordered"><thead><tr>' +
            '<th>Group Code</th><th>Group Name</th><th>Subjects</th></tr></thead><tbody>';

        availableGroups.forEach(function(group) {
            var subjects = 'No subjects';
            
            if (group.subjects && Array.isArray(group.subjects) && group.subjects.length > 0) {
                subjects = group.subjects.map(function(s) {
                    return (s.subject_code || '') + ' - ' + (s.subject_name || '');
                }).join(', ');
            }
            
            html += '<tr>' +
                '<td><strong>' + (group.group_code || 'N/A') + '</strong></td>' +
                '<td>' + (group.group_name || 'N/A') + '</td>' +
                '<td><small>' + subjects + '</small></td>' +
                '</tr>';
        });

        html += '</tbody></table>';
        $('#available-groups-container').html(html);
        console.log('Groups displayed successfully');
    }

    function saveRule() {
        console.log('Saving rule...');
        
        // Clear previous errors
        $('.help-block').text('');

        // Disable save button
        var $saveBtn = $('#saveRuleBtn');
        $saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        // Build and set JSON
        var rulesJson = buildRulesJson();
        
        var formData = {
            _token: $('#csrf_token').val(),
            rule_name: $('#rule_name').val(),
            level_id: $('#level_id').val(),
            type: $('#type').val(),
            description: $('#description').val(),
            is_active: $('#is_active').is(':checked') ? 1 : 0,
            rules: JSON.stringify(rulesJson)
        };

        console.log('Form data:', formData);

        var url = "{{ route('admin.subject-group-rules.store') }}";
        console.log('Posting to:', url);

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    toastr.success(response.message || 'Rule created successfully');
                    $('#createRuleModal').modal('hide');
                    if (typeof rulesTable !== 'undefined' && rulesTable) {
                        rulesTable.ajax.reload();
                    }
                }
                $saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
            },
            error: function(xhr, status, error) {
                console.error('Error response:', xhr.responseText);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayErrors(xhr.responseJSON.errors);
                } else {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? 
                        xhr.responseJSON.message : 'Error creating rule. Please check your inputs.';
                    toastr.error(message);
                }
                $saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
            }
        });
    }

    function displayErrors(errors) {
        $.each(errors, function(key, value) {
            var errorMsg = Array.isArray(value) ? value[0] : value;
            $('#error_' + key).text(errorMsg).addClass('text-danger');
        });
    }

    function addRequiredGroup() {
        if (availableGroups.length === 0) {
            toastr.warning('Please select a level first');
            return;
        }

        var id = 'req-' + (++requiredGroupsCounter);
        var groupOptions = '<option value="">Select Group</option>';
        availableGroups.forEach(function(group) {
            groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
        });

        var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
            '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
            '<i class="fa fa-trash"></i></button>' +
            '<div class="row">' +
            '<div class="col-md-5">' +
            '<label>Group</label>' +
            '<select class="form-control rule-input required-group-code">' + groupOptions + '</select>' +
            '</div>' +
            '<div class="col-md-5">' +
            '<label>Min Count</label>' +
            '<input type="number" class="form-control rule-input required-group-min" min="1" value="1">' +
            '</div>' +
            '<div class="col-md-5">' +
            '<label>Max Count</label>' +
            '<input type="number" class="form-control rule-input required-group-max" min="1" placeholder="No limit">' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#required-groups-container').append(html);
        updateJsonPreview();
    }

    function addForbiddenGroup() {
        if (availableGroups.length === 0) {
            toastr.warning('Please select a level first');
            return;
        }

        var id = 'forb-' + (++forbiddenGroupsCounter);
        var groupOptions = '<option value="">Select Group</option>';
        availableGroups.forEach(function(group) {
            groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
        });

        var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
            '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
            '<i class="fa fa-trash"></i></button>' +
            '<select class="form-control rule-input forbidden-group-code">' + groupOptions + '</select>' +
            '</div>';

        $('#forbidden-groups-container').append(html);
        updateJsonPreview();
    }

    function addConstraint() {
        if (availableGroups.length === 0) {
            toastr.warning('Please select a level first');
            return;
        }

        var id = 'const-' + (++constraintsCounter);
        var groupOptions = '<option value="">Select Group</option>';
        availableGroups.forEach(function(group) {
            groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
        });

        var html = '<div class="panel panel-default" id="' + id + '">' +
            '<div class="panel-heading">' +
            '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
            '<i class="fa fa-trash"></i></button>' +
            'Constraint ' + constraintsCounter +
            '</div>' +
            '<div class="panel-body">' +
            '<div class="form-group">' +
            '<label>Constraint Type</label>' +
            '<select class="form-control rule-input constraint-type" onchange="updateConstraintFields(this)">' +
            '<option value="">Select Type</option>' +
            '<option value="at_least_one_from_multiple">At Least One From Multiple</option>' +
            '<option value="mutually_exclusive">Mutually Exclusive</option>' +
            '<option value="conditional_required">Conditional Required</option>' +
            '<option value="min_total_from_groups">Min Total From Groups</option>' +
            '</select>' +
            '</div>' +
            '<div class="constraint-fields"></div>' +
            '<div class="form-group">' +
            '<label>Custom Message (Optional)</label>' +
            '<input type="text" class="form-control rule-input constraint-message" placeholder="Error message">' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#constraints-container').append(html);
        updateJsonPreview();
    }

    function updateConstraintFields(selectElement) {
        var type = $(selectElement).val();
        var container = $(selectElement).closest('.panel-body').find('.constraint-fields');
        var groupOptions = '<option value="">Select Group</option>';

        availableGroups.forEach(function(group) {
            groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
        });

        var html = '';

        switch (type) {
            case 'at_least_one_from_multiple':
            case 'mutually_exclusive':
                html = '<div class="form-group">' +
                    '<label>Groups (Select multiple)</label>' +
                    '<select class="form-control rule-input constraint-groups" multiple size="5">' + groupOptions +
                    '</select>' +
                    '</div>';
                break;

            case 'conditional_required':
                html = '<div class="row">' +
                    '<div class="col-md-6">' +
                    '<label>If Group</label>' +
                    '<select class="form-control rule-input constraint-if-group">' + groupOptions + '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                    '<label>Then Group</label>' +
                    '<select class="form-control rule-input constraint-then-group">' + groupOptions + '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label>Min Count</label>' +
                    '<input type="number" class="form-control rule-input constraint-min-count" value="1" min="1">' +
                    '</div>';
                break;

            case 'min_total_from_groups':
                html = '<div class="form-group">' +
                    '<label>Groups (Select multiple)</label>' +
                    '<select class="form-control rule-input constraint-groups" multiple size="5">' + groupOptions +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label>Minimum Total</label>' +
                    '<input type="number" class="form-control rule-input constraint-min-total" value="1" min="1">' +
                    '</div>';
                break;
        }

        container.html(html);
        updateJsonPreview();
    }

    function buildRulesJson() {
        var rules = {};

        // Subject count constraints
        var minSubjects = parseInt($('#min_subjects').val());
        var maxSubjects = parseInt($('#max_subjects').val());
        if (minSubjects) rules.min_subjects = minSubjects;
        if (maxSubjects) rules.max_subjects = maxSubjects;

        // Required groups
        rules.required_groups = [];
        $('.required-group-code').each(function() {
            var groupCode = $(this).val();
            if (groupCode) {
                var minCount = parseInt($(this).closest('.well').find('.required-group-min').val()) || 1;
                var maxCount = parseInt($(this).closest('.well').find('.required-group-max').val());

                var group = {
                    group_code: groupCode,
                    min_count: minCount
                };
                if (maxCount) group.max_count = maxCount;

                rules.required_groups.push(group);
            }
        });

        // Forbidden groups
        rules.forbidden_groups = [];
        $('.forbidden-group-code').each(function() {
            var groupCode = $(this).val();
            if (groupCode) {
                rules.forbidden_groups.push(groupCode);
            }
        });

        // Group constraints
        rules.group_constraints = [];
        $('.constraint-type').each(function() {
            var type = $(this).val();
            if (!type) return;

            var panel = $(this).closest('.panel-body');
            var constraint = {
                type: type
            };
            var message = panel.find('.constraint-message').val();
            if (message) constraint.message = message;

            switch (type) {
                case 'at_least_one_from_multiple':
                case 'mutually_exclusive':
                    var groups = panel.find('.constraint-groups').val() || [];
                    if (groups.length > 0) {
                        constraint.groups = groups;
                        rules.group_constraints.push(constraint);
                    }
                    break;

                case 'conditional_required':
                    var ifGroup = panel.find('.constraint-if-group').val();
                    var thenGroup = panel.find('.constraint-then-group').val();
                    var minCount = parseInt(panel.find('.constraint-min-count').val()) || 1;
                    if (ifGroup && thenGroup) {
                        constraint.if_group = ifGroup;
                        constraint.then_group = thenGroup;
                        constraint.min_count = minCount;
                        rules.group_constraints.push(constraint);
                    }
                    break;

                case 'min_total_from_groups':
                    var groups = panel.find('.constraint-groups').val() || [];
                    var minTotal = parseInt(panel.find('.constraint-min-total').val()) || 1;
                    if (groups.length > 0) {
                        constraint.groups = groups;
                        constraint.min_total = minTotal;
                        rules.group_constraints.push(constraint);
                    }
                    break;
            }
        });

        return rules;
    }

    function updateJsonPreview() {
        var rules = buildRulesJson();
        $('#json-preview').text(JSON.stringify(rules, null, 2));
    }
</script>
@endpush