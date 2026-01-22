@extends('layouts.admin')

@section('content')
<div class="main">
    <div class="main-content">
        <div class="container-fluid">
            <h3 class="page-title">Edit Validation Rule</h3>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Edit: {{ $rule->rule_name }}</h3>
                        </div>
                        <div class="panel-body">
                            <form id="editRuleForm">
                                @csrf
                                @method('PUT')
                                
                                <!-- Basic Information -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rule Name *</label>
                                            <input type="text" name="rule_name" class="form-control" value="{{ $rule->rule_name }}" required>
                                            <span class="help-block text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_active" value="1" {{ $rule->is_active ? 'checked' : '' }}> Active
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="3">{{ $rule->description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Level *</label>
                                            <select name="level_id" id="level_id" class="form-control" required>
                                                <option value="">Select Level</option>
                                                @foreach($levels as $level)
                                                    <option value="{{ $level->id }}" {{ $rule->level_id == $level->id ? 'selected' : '' }}>
                                                        {{ $level->level }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block text-danger"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Registration Type *</label>
                                            <select name="type" class="form-control" required>
                                                <option value="">Select Type</option>
                                                <option value="1" {{ $rule->type == 1 ? 'selected' : '' }}>Full Registration</option>
                                                <option value="2" {{ $rule->type == 2 ? 'selected' : '' }}>Partial Registration</option>
                                                <option value="3" {{ $rule->type == 3 ? 'selected' : '' }}>Private Registration</option>
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
                                                    <input type="number" id="min_subjects" class="form-control" min="0" value="{{ $rule->rules['min_subjects'] ?? '' }}">
                                                    <small class="text-muted">Leave blank for no minimum</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Maximum Subjects</label>
                                                    <input type="number" id="max_subjects" class="form-control" min="0" value="{{ $rule->rules['max_subjects'] ?? '' }}">
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
                                            @if($groups->count() > 0)
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Group Code</th>
                                                            <th>Group Name</th>
                                                            <th>Subjects</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($groups as $group)
                                                            <tr>
                                                                <td><strong>{{ $group->group_code }}</strong></td>
                                                                <td>{{ $group->group_name }}</td>
                                                                <td>
                                                                    <small>
                                                                        @foreach($group->subjects as $subject)
                                                                            {{ $subject->subject_code }} - {{ $subject->subject_name }}{{ !$loop->last ? ', ' : '' }}
                                                                        @endforeach
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-warning">
                                                    No subject groups found for this level
                                                </div>
                                            @endif
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
                                            <!-- Required groups will be loaded here -->
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
                                            <!-- Forbidden groups will be loaded here -->
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
                                            <!-- Constraints will be loaded here -->
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

                                <!-- Hidden field for JSON rules -->
                                <input type="hidden" name="rules" id="rules-json">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Rule
                                    </button>
                                    <a href="{{ route('admin.subject-group-rules.index') }}" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var availableGroups = @json($groups);
var existingRules = @json($rule->rules);
var requiredGroupsCounter = 0;
var forbiddenGroupsCounter = 0;
var constraintsCounter = 0;

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-center",
    timeOut: "5000"
};

$(document).ready(function() {
    // Load existing rules
    loadExistingRules();

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
    $('#editRuleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Build and set JSON
        var rulesJson = buildRulesJson();
        $('#rules-json').val(JSON.stringify(rulesJson));

        var formData = $(this).serialize();

        $.ajax({
            url: "{{ route('admin.subject-group-rules.update', $rule->id) }}",
            method: "POST",
            data: formData,
            success: function(data) {
                if (data.errors) {
                    printErrorMsg('#editRuleForm', data.errors);
                } else {
                    toastr.success(data.success);
                    window.location.href = "{{ route('admin.subject-group-rules.index') }}";
                }
            },
            error: function(xhr) {
                toastr.error('Error updating rule. Please check your inputs.');
            }
        });
    });

    // Initial JSON preview
    updateJsonPreview();
});

function loadExistingRules() {
    // Load required groups
    if (existingRules.required_groups && existingRules.required_groups.length > 0) {
        existingRules.required_groups.forEach(function(group) {
            addRequiredGroup(group);
        });
    }

    // Load forbidden groups
    if (existingRules.forbidden_groups && existingRules.forbidden_groups.length > 0) {
        existingRules.forbidden_groups.forEach(function(groupCode) {
            addForbiddenGroup(groupCode);
        });
    }

    // Load constraints
    if (existingRules.group_constraints && existingRules.group_constraints.length > 0) {
        existingRules.group_constraints.forEach(function(constraint) {
            addConstraint(constraint);
        });
    }
}

function addRequiredGroup(existingData = null) {
    var id = 'req-' + (++requiredGroupsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    availableGroups.forEach(function(group) {
        var selected = existingData && existingData.group_code === group.group_code ? 'selected' : '';
        groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var minCount = existingData ? existingData.min_count : 1;
    var maxCount = existingData && existingData.max_count ? existingData.max_count : '';

    var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               '<div class="row">' +
               '<div class="col-md-6">' +
               '<label>Group</label>' +
               '<select class="form-control rule-input required-group-code">' + groupOptions + '</select>' +
               '</div>' +
               '<div class="col-md-3">' +
               '<label>Min Count</label>' +
               '<input type="number" class="form-control rule-input required-group-min" min="1" value="' + minCount + '">' +
               '</div>' +
               '<div class="col-md-3">' +
               '<label>Max Count (Optional)</label>' +
               '<input type="number" class="form-control rule-input required-group-max" min="1" value="' + maxCount + '" placeholder="No limit">' +
               '</div>' +
               '</div>' +
               '</div>';

    $('#required-groups-container').append(html);
    updateJsonPreview();
}

function addForbiddenGroup(existingGroupCode = null) {
    var id = 'forb-' + (++forbiddenGroupsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    availableGroups.forEach(function(group) {
        var selected = existingGroupCode === group.group_code ? 'selected' : '';
        groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               '<select class="form-control rule-input forbidden-group-code">' + groupOptions + '</select>' +
               '</div>';

    $('#forbidden-groups-container').append(html);
    updateJsonPreview();
}

function addConstraint(existingData = null) {
    var id = 'const-' + (++constraintsCounter);
    var groupOptions = '<option value="">Select Group</option>';
    availableGroups.forEach(function(group) {
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
               '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="$(\'#' + id + '\').remove(); updateJsonPreview();">' +
               '<i class="fa fa-trash"></i></button>' +
               'Constraint ' + constraintsCounter +
               '</div>' +
               '<div class="panel-body">' +
               '<div class="form-group">' +
               '<label>Constraint Type</label>' +
               '<select class="form-control rule-input constraint-type" onchange="updateConstraintFields(this)">' +
               typeOptions +
               '</select>' +
               '</div>' +
               '<div class="constraint-fields"></div>' +
               '<div class="form-group">' +
               '<label>Custom Message (Optional)</label>' +
               '<input type="text" class="form-control rule-input constraint-message" value="' + message + '" placeholder="Error message">' +
               '</div>' +
               '</div>' +
               '</div>';

    $('#constraints-container').append(html);

    // If there's existing data, populate the fields
    if (existingData) {
        setTimeout(function() {
            var panel = $('#' + id);
            updateConstraintFields(panel.find('.constraint-type')[0], existingData);
        }, 100);
    }

    updateJsonPreview();
}

function updateConstraintFields(selectElement, existingData = null) {
    var type = $(selectElement).val();
    var container = $(selectElement).closest('.panel-body').find('.constraint-fields');
    var groupOptions = '<option value="">Select Group</option>';
    
    availableGroups.forEach(function(group) {
        groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
    });

    var html = '';

    switch(type) {
        case 'at_least_one_from_multiple':
        case 'mutually_exclusive':
            var selectedGroups = existingData && existingData.groups ? existingData.groups : [];
            var multipleOptions = '';
            availableGroups.forEach(function(group) {
                var selected = selectedGroups.includes(group.group_code) ? 'selected' : '';
                multipleOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
            });
            html = '<div class="form-group">' +
                   '<label>Groups (Select multiple)</label>' +
                   '<select class="form-control rule-input constraint-groups" multiple size="5">' + multipleOptions + '</select>' +
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
                   '<select class="form-control rule-input constraint-if-group">' + ifGroupOptions + '</select>' +
                   '</div>' +
                   '<div class="col-md-6">' +
                   '<label>Then Group</label>' +
                   '<select class="form-control rule-input constraint-then-group">' + thenGroupOptions + '</select>' +
                   '</div>' +
                   '</div>' +
                   '<div class="form-group">' +
                   '<label>Min Count</label>' +
                   '<input type="number" class="form-control rule-input constraint-min-count" value="' + minCount + '" min="1">' +
                   '</div>';
            break;

        case 'min_total_from_groups':
            var selectedGroups = existingData && existingData.groups ? existingData.groups : [];
            var minTotal = existingData ? existingData.min_total : 1;
            var multipleOptions = '';
            availableGroups.forEach(function(group) {
                var selected = selectedGroups.includes(group.group_code) ? 'selected' : '';
                multipleOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
            });
            html = '<div class="form-group">' +
                   '<label>Groups (Select multiple)</label>' +
                   '<select class="form-control rule-input constraint-groups" multiple size="5">' + multipleOptions + '</select>' +
                   '</div>' +
                   '<div class="form-group">' +
                   '<label>Minimum Total</label>' +
                   '<input type="number" class="form-control rule-input constraint-min-total" value="' + minTotal + '" min="1">' +
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
        var constraint = { type: type };
        var message = panel.find('.constraint-message').val();
        if (message) constraint.message = message;

        switch(type) {
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

function printErrorMsg(parent, errors) {
    $(parent + ' .help-block').text('');
    $.each(errors, function(key, value) {
        $(parent + ' [name="' + key + '"]').next('.help-block').text(value[0]).addClass('text-danger');
    });
}
</script>
@endsection