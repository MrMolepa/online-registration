{{-- Edit Validation Rule Modal --}}
<div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog" aria-labelledby="editRuleModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="editRuleModalLabel">Edit Validation Rule</h4>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="editRuleForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden field for rule ID -->
                    <input type="hidden" id="edit_rule_id">
                    
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Rule Name *</label>
                                <input type="text" name="rule_name" id="edit_rule_name" class="form-control">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Active
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Level *</label>
                                <select name="level_id" id="edit_level_id" class="form-control">
                                    <option value="">Select Level</option>
                                </select>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Registration Type *</label>
                                <select name="type" id="edit_type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="1">Full Registration</option>
                                    <option value="2">Partial Registration</option>
                                    <option value="3">Private Registration</option>
                                </select>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                    </div>

                    <hr>

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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Minimum Subjects</label>
                                        <input type="number" id="edit_min_subjects" class="form-control" min="0" placeholder="e.g., 5">
                                        <small class="text-muted">Leave blank for no minimum</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Maximum Subjects</label>
                                        <input type="number" id="edit_max_subjects" class="form-control" min="0" placeholder="e.g., 10">
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
                            <div id="edit-available-groups-container">
                                <p class="text-muted">Loading groups...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Required Groups -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Required Groups</h4>
                        </div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-sm btn-success" id="edit-add-required-group">
                                <i class="fa fa-plus"></i> Add Required Group
                            </button>
                            <div id="edit-required-groups-container" style="margin-top: 15px;">
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
                            <button type="button" class="btn btn-sm btn-danger" id="edit-add-forbidden-group">
                                <i class="fa fa-plus"></i> Add Forbidden Group
                            </button>
                            <div id="edit-forbidden-groups-container" style="margin-top: 15px;">
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
                            <button type="button" class="btn btn-sm btn-primary" id="edit-add-constraint">
                                <i class="fa fa-plus"></i> Add Constraint
                            </button>
                            <div id="edit-constraints-container" style="margin-top: 15px;">
                                <!-- Constraints will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- JSON Preview -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                JSON Preview
                                <button type="button" class="btn btn-xs btn-default pull-right" id="edit-toggle-json">
                                    <i class="fa fa-eye"></i> Show/Hide
                                </button>
                            </h4>
                        </div>
                        <div class="panel-body" id="edit-json-preview-container" style="display: none;">
                            <pre id="edit-json-preview" style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></pre>
                        </div>
                    </div>

                    <!-- Hidden field for JSON rules -->
                    <input type="hidden" name="rules" id="edit-rules-json">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="submit" form="editRuleForm" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Rule
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var editAvailableGroups = [];
var editExistingRules = {};
var editRequiredGroupsCounter = 0;
var editForbiddenGroupsCounter = 0;
var editConstraintsCounter = 0;

function populateEditModal(data) {
    // Reset counters
    editRequiredGroupsCounter = 0;
    editForbiddenGroupsCounter = 0;
    editConstraintsCounter = 0;

    // Set basic fields
    $('#edit_rule_id').val(data.rule.id);
    $('#edit_rule_name').val(data.rule.rule_name);
    $('#edit_description').val(data.rule.description);
    $('#edit_is_active').prop('checked', data.rule.is_active);
    $('#edit_type').val(data.rule.type);
    $('#edit_min_subjects').val(data.rule.rules.min_subjects || '');
    $('#edit_max_subjects').val(data.rule.rules.max_subjects || '');

    // Populate levels dropdown
    var levelOptions = '<option value="">Select Level</option>';
    data.levels.forEach(function(level) {
        var selected = level.id === data.rule.level_id ? 'selected' : '';
        levelOptions += '<option value="' + level.id + '" ' + selected + '>' + level.level + '</option>';
    });
    $('#edit_level_id').html(levelOptions);

    // Store available groups and existing rules
    editAvailableGroups = data.groups;
    editExistingRules = data.rule.rules;

    // Display available groups
    displayEditAvailableGroups();

    // Clear previous dynamic content
    $('#edit-required-groups-container').empty();
    $('#edit-forbidden-groups-container').empty();
    $('#edit-constraints-container').empty();
    $('#editRuleForm .help-block').text('');

    // Load existing rules
    loadEditExistingRules();

    // Update JSON preview
    updateEditJsonPreview();
}

function displayEditAvailableGroups() {
    if (editAvailableGroups.length === 0) {
        $('#edit-available-groups-container').html(
            '<div class="alert alert-warning">No subject groups found for this level.</div>'
        );
        return;
    }

    var html = '<table class="table table-sm table-bordered"><thead><tr>' +
               '<th>Group Code</th><th>Group Name</th><th>Subjects</th></tr></thead><tbody>';
    
    editAvailableGroups.forEach(function(group) {
        var subjects = group.subjects.map(s => s.subject_code + ' - ' + s.subject_name).join(', ');
        html += '<tr>' +
                '<td><strong>' + group.group_code + '</strong></td>' +
                '<td>' + group.group_name + '</td>' +
                '<td><small>' + subjects + '</small></td>' +
                '</tr>';
    });
    
    html += '</tbody></table>';
    $('#edit-available-groups-container').html(html);
}

function loadEditExistingRules() {
    // Load required groups
    if (editExistingRules.required_groups && editExistingRules.required_groups.length > 0) {
        editExistingRules.required_groups.forEach(function(group) {
            addEditRequiredGroup(group);
        });
    }

    // Load forbidden groups
    if (editExistingRules.forbidden_groups && editExistingRules.forbidden_groups.length > 0) {
        editExistingRules.forbidden_groups.forEach(function(groupCode) {
            addEditForbiddenGroup(groupCode);
        });
    }

    // Load constraints
    if (editExistingRules.group_constraints && editExistingRules.group_constraints.length > 0) {
        editExistingRules.group_constraints.forEach(function(constraint) {
            addEditConstraint(constraint);
        });
    }
}

$(document).ready(function() {
    // Reset edit form when modal is closed
    $('#editRuleModal').on('hidden.bs.modal', function() {
        $('#editRuleForm')[0].reset();
        $('#editRuleForm .help-block').text('');
        $('#edit-available-groups-container').html('<p class="text-muted">Loading groups...</p>');
        $('#edit-required-groups-container').empty();
        $('#edit-forbidden-groups-container').empty();
        $('#edit-constraints-container').empty();
        $('#edit-json-preview-container').hide();
        $('#edit-json-preview').text('');
        editAvailableGroups = [];
        editExistingRules = {};
        editRequiredGroupsCounter = 0;
        editForbiddenGroupsCounter = 0;
        editConstraintsCounter = 0;
    });

    // Toggle JSON preview
    $(document).on('click', '#edit-toggle-json', function() {
        $('#edit-json-preview-container').slideToggle();
        updateEditJsonPreview();
    });

    // Add required group
    $(document).on('click', '#edit-add-required-group', function() {
        addEditRequiredGroup();
    });

    // Add forbidden group
    $(document).on('click', '#edit-add-forbidden-group', function() {
        addEditForbiddenGroup();
    });

    // Add constraint
    $(document).on('click', '#edit-add-constraint', function() {
        addEditConstraint();
    });

    // Update JSON on any change
    $(document).on('change input', '#edit_min_subjects, #edit_max_subjects, #editRuleModal .rule-input', function() {
        updateEditJsonPreview();
    });

    // Form submission
    $(document).on('submit', '#editRuleForm', function(e) {
        e.preventDefault();
        
        var ruleId = $('#edit_rule_id').val();
        
        // Build and set JSON
        var rulesJson = buildEditRulesJson();
        $('#edit-rules-json').val(JSON.stringify(rulesJson));

        var formData = $(this).serialize();

        $.ajax({
            url: "{{ route('admin.subject-group-rules.update', ':id') }}".replace(':id', ruleId),
            method: "POST",
            data: formData,
            success: function(data) {
                if (data.errors) {
                    printErrorMsg('#editRuleForm', data.errors);
                } else {
                    toastr.success(data.success);
                    $('#editRuleModal').modal('hide');
                    if (rulesTable) {
                        rulesTable.ajax.reload();
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Error updating rule. Please check your inputs.');
            }
        });
    });
});

function addEditRequiredGroup(existingData = null) {
    var id = 'edit-req-' + (++editRequiredGroupsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    editAvailableGroups.forEach(function(group) {
        var selected = existingData && existingData.group_code === group.group_code ? 'selected' : '';
        groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var minCount = existingData ? existingData.min_count : 1;
    var maxCount = existingData && existingData.max_count ? existingData.max_count : '';

    var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateEditJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               '<div class="row">' +
               '<div class="col-md-4">' +
               '<label>Group</label>' +
               '<select class="form-control rule-input edit-required-group-code">' + groupOptions + '</select>' +
               '</div>' +
               '<div class="col-md-4">' +
               '<label>Min Count</label>' +
               '<input type="number" class="form-control rule-input edit-required-group-min" min="1" value="' + minCount + '">' +
               '</div>' +
               '<div class="col-md-4">' +
               '<label>Max Count (Optional)</label>' +
               '<input type="number" class="form-control rule-input edit-required-group-max" min="1" value="' + maxCount + '" placeholder="No limit">' +
               '</div>' +
               '</div>' +
               '</div>';

    $('#edit-required-groups-container').append(html);
    updateEditJsonPreview();
}

function addEditForbiddenGroup(existingGroupCode = null) {
    var id = 'edit-forb-' + (++editForbiddenGroupsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    editAvailableGroups.forEach(function(group) {
        var selected = existingGroupCode === group.group_code ? 'selected' : '';
        groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateEditJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               '<select class="form-control rule-input edit-forbidden-group-code">' + groupOptions + '</select>' +
               '</div>';

    $('#edit-forbidden-groups-container').append(html);
    updateEditJsonPreview();
}

function addEditConstraint(existingData = null) {
    var id = 'edit-const-' + (++editConstraintsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    editAvailableGroups.forEach(function(group) {
        groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var selectedType = existingData ? existingData.type : '';
    var message = existingData && existingData.message ? existingData.message : '';

    var typeOptions = '<option value="">Select Type</option>' +
                     '<option value="at_least_one_from_multiple" ' + (selectedType === 'at_least_one_from_multiple' ? 'selected' : '') + '>At Least One From Multiple</option>' +
                     '<option value="mutually_exclusive" ' + (selectedType === 'mutually_exclusive' ? 'selected' : '') + '>Mutually Exclusive</option>' +
                     '<option value="conditional_required" ' + (selectedType === 'conditional_required' ? 'selected' : '') + '>Conditional Required</option>' +
                     '<option value="min_total_from_groups" ' + (selectedType === 'min_total_from_groups' ? 'selected' : '') + '>Min Total From Groups</option>';

    var html = '<div class="panel panel-default" id="' + id + '">' +
               '<div class="panel-heading">' +
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateEditJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               'Constraint ' + editConstraintsCounter +
               '</div>' +
               '<div class="panel-body">' +
               '<div class="form-group">' +
               '<label>Constraint Type</label>' +
               '<select class="form-control rule-input edit-constraint-type" onchange="updateEditConstraintFields(this)">' +
               typeOptions +
               '</select>' +
               '</div>' +
               '<div class="edit-constraint-fields"></div>' +
               '<div class="form-group">' +
               '<label>Custom Message (Optional)</label>' +
               '<input type="text" class="form-control rule-input edit-constraint-message" value="' + message + '" placeholder="Error message">' +
               '</div>' +
               '</div>' +
               '</div>';

    $('#edit-constraints-container').append(html);

    // If there's existing data, populate the fields
    if (existingData) {
        setTimeout(function() {
            var panel = $('#' + id);
            updateEditConstraintFields(panel.find('.edit-constraint-type')[0], existingData);
        }, 100);
    }

    updateEditJsonPreview();
}

function updateEditConstraintFields(selectElement, existingData = null) {
    var type = $(selectElement).val();
    var container = $(selectElement).closest('.panel-body').find('.edit-constraint-fields');
    var groupOptions = '<option value="">Select Group</option>';
    
    editAvailableGroups.forEach(function(group) {
        groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var html = '';

    switch(type) {
        case 'at_least_one_from_multiple':
        case 'mutually_exclusive':
            var selectedGroups = existingData && existingData.groups ? existingData.groups : [];
            var multipleOptions = '';
            editAvailableGroups.forEach(function(group) {
                var selected = selectedGroups.includes(group.group_code) ? 'selected' : '';
                multipleOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
            });
            html = '<div class="form-group">' +
                   '<label>Groups (Select multiple)</label>' +
                   '<select class="form-control rule-input edit-constraint-groups" multiple size="5">' + multipleOptions + '</select>' +
                   '</div>';
            break;

        case 'conditional_required':
            var ifGroup = existingData ? existingData.if_group : '';
            var thenGroup = existingData ? existingData.then_group : '';
            var minCount = existingData ? existingData.min_count : 1;
            
            var ifGroupOptions = groupOptions.replace('value="' + ifGroup + '"', 'value="' + ifGroup + '" selected');
            var thenGroupOptions = groupOptions.replace('value="' + thenGroup + '"', 'value="' + thenGroup + '" selected');
            
            html = '<div class="row">' +
                   '<div class="col-md-6">' +
                   '<label>If Group</label>' +
                   '<select class="form-control rule-input edit-constraint-if-group">' + ifGroupOptions + '</select>' +
                   '</div>' +
                   '<div class="col-md-6">' +
                   '<label>Then Group</label>' +
                   '<select class="form-control rule-input edit-constraint-then-group">' + thenGroupOptions + '</select>' +
                   '</div>' +
                   '</div>' +
                   '<div class="form-group">' +
                   '<label>Min Count</label>' +
                   '<input type="number" class="form-control rule-input edit-constraint-min-count" value="' + minCount + '" min="1">' +
                   '</div>';
            break;

        case 'min_total_from_groups':
            var selectedGroups = existingData && existingData.groups ? existingData.groups : [];
            var minTotal = existingData ? existingData.min_total : 1;
            var multipleOptions = '';
            editAvailableGroups.forEach(function(group) {
                var selected = selectedGroups.includes(group.group_code) ? 'selected' : '';
                multipleOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
            });
            html = '<div class="form-group">' +
                   '<label>Groups (Select multiple)</label>' +
                   '<select class="form-control rule-input edit-constraint-groups" multiple size="5">' + multipleOptions + '</select>' +
                   '</div>' +
                   '<div class="form-group">' +
                   '<label>Minimum Total</label>' +
                   '<input type="number" class="form-control rule-input edit-constraint-min-total" value="' + minTotal + '" min="1">' +
                   '</div>';
            break;
    }

    container.html(html);
    updateEditJsonPreview();
}

function buildEditRulesJson() {
    var rules = {};

    // Subject count constraints
    var minSubjects = parseInt($('#edit_min_subjects').val());
    var maxSubjects = parseInt($('#edit_max_subjects').val());
    if (minSubjects) rules.min_subjects = minSubjects;
    if (maxSubjects) rules.max_subjects = maxSubjects;

    // Required groups
    rules.required_groups = [];
    $('.edit-required-group-code').each(function() {
        var groupCode = $(this).val();
        if (groupCode) {
            var minCount = parseInt($(this).closest('.well').find('.edit-required-group-min').val()) || 1;
            var maxCount = parseInt($(this).closest('.well').find('.edit-required-group-max').val());
            
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
    $('.edit-forbidden-group-code').each(function() {
        var groupCode = $(this).val();
        if (groupCode) {
            rules.forbidden_groups.push(groupCode);
        }
    });

    // Group constraints
    rules.group_constraints = [];
    $('.edit-constraint-type').each(function() {
        var type = $(this).val();
        if (!type) return;

        var panel = $(this).closest('.panel-body');
        var constraint = { type: type };
        var message = panel.find('.edit-constraint-message').val();
        if (message) constraint.message = message;

        switch(type) {
            case 'at_least_one_from_multiple':
            case 'mutually_exclusive':
                var groups = panel.find('.edit-constraint-groups').val() || [];
                if (groups.length > 0) {
                    constraint.groups = groups;
                    rules.group_constraints.push(constraint);
                }
                break;

            case 'conditional_required':
                var ifGroup = panel.find('.edit-constraint-if-group').val();
                var thenGroup = panel.find('.edit-constraint-then-group').val();
                var minCount = parseInt(panel.find('.edit-constraint-min-count').val()) || 1;
                if (ifGroup && thenGroup) {
                    constraint.if_group = ifGroup;
                    constraint.then_group = thenGroup;
                    constraint.min_count = minCount;
                    rules.group_constraints.push(constraint);
                }
                break;

            case 'min_total_from_groups':
                var groups = panel.find('.edit-constraint-groups').val() || [];
                var minTotal = parseInt(panel.find('.edit-constraint-min-total').val()) || 1;
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

function updateEditJsonPreview() {
    var rules = buildEditRulesJson();
    $('#edit-json-preview').text(JSON.stringify(rules, null, 2));
}
</script>