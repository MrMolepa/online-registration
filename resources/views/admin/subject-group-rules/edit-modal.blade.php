{{-- Edit Validation Rule Modal --}}
<div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog" aria-labelledby="editRuleModalLabel">
    <div class="modal-dialog modal-md" role="document" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="editRuleModalLabel">Edit Validation Rule</h4>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="editRuleForm" onsubmit="return false;">
                    @csrf
                    @method('PUT')

                    <!-- Hidden field for rule ID -->
                    <input type="hidden" id="edit_rule_id">

                    <!-- Basic Information -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Basic Information</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Rule Name *</label>
                                        <input type="text" name="rule_name" id="edit_rule_name" class="form-control">
                                        <span class="help-block text-danger"></span>
                                    </div>
                                </div>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                                            Active
                                        </label>
                                    </div>
                                </div>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Minimum Subjects</label>
                                        <input type="number" id="edit_min_subjects" class="form-control" min="0"
                                            placeholder="e.g., 5">
                                        <small class="text-muted">Leave blank for no minimum</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Maximum Subjects</label>
                                        <input type="number" id="edit_max_subjects" class="form-control" min="0"
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
                            <button type="button" class="btn btn-sm btn-success" onclick="addEditRequiredGroup()">
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
                            <button type="button" class="btn btn-sm btn-danger" onclick="addEditForbiddenGroup()">
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
                            <button type="button" class="btn btn-sm btn-primary" onclick="addEditConstraint()">
                                <i class="fa fa-plus"></i> Add Constraint
                            </button>
                            <div id="edit-constraints-container" style="margin-top: 15px;">
                                <!-- Constraints will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Incompatible Subject Pairs -->
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4 class="panel-title">Incompatible Subject Pairs</h4>
                        </div>
                        <div class="panel-body">
                            <p class="text-muted small">Pairs of subjects that <strong>cannot</strong> be selected
                                together.</p>
                            <button type="button" class="btn btn-sm btn-warning" onclick="addEditIncompatiblePair()">
                                <i class="fa fa-plus"></i> Add Incompatible Pair
                            </button>
                            <div id="edit-incompatible-pairs-container" style="margin-top: 15px;"></div>
                        </div>
                    </div>

                    <!-- JSON Preview -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                JSON Preview
                                <button type="button" class="btn btn-xs btn-default pull-right"
                                    onclick="toggleEditJsonPreview()">
                                    <i class="fa fa-eye"></i> Show/Hide
                                </button>
                            </h4>
                        </div>
                        <div class="panel-body" id="edit-json-preview-container" style="display: none;">
                            <pre id="edit-json-preview"
                                style="background: #f5f5f5; padding: 10px; border-radius: 4px;"></pre>
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
                <button type="button" class="btn btn-primary" onclick="submitEditRule()">
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
    var editIncompatiblePairsCounter = 0;
    var updateRuleBaseUrl = "{{ route('admin.subject-group-rules.index') }}";
    var csrfToken = "{{ csrf_token() }}";

    function submitEditRule() {
        var ruleId = document.getElementById('edit_rule_id').value;

        if (!ruleId) {
            toastr.error('Rule ID is missing');
            return;
        }

        // Clear previous errors
        var helpBlocks = document.querySelectorAll('#editRuleForm .help-block');
        helpBlocks.forEach(function (block) {
            block.textContent = '';
        });

        // Build rules JSON
        var rulesJson = buildEditRulesJson();

        // Prepare form data
        var formData = {
            _token: csrfToken,
            _method: 'PUT',
            rule_name: document.getElementById('edit_rule_name').value,
            level_id: document.getElementById('edit_level_id').value,
            type: document.getElementById('edit_type').value,
            is_active: document.getElementById('edit_is_active').checked ? 1 : 0,
            rules: JSON.stringify(rulesJson)
        };

        var btnUpdate = event.target;
        var originalHtml = btnUpdate.innerHTML;
        btnUpdate.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
        btnUpdate.disabled = true;

        $.ajax({
            url: updateRuleBaseUrl + '/' + ruleId,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                btnUpdate.innerHTML = originalHtml;
                btnUpdate.disabled = false;

                if (response.success) {
                    toastr.success(response.message || 'Rule updated successfully');
                    $('#editRuleModal').modal('hide');

                    if (typeof rulesTable !== 'undefined' && rulesTable) {
                        rulesTable.ajax.reload(null, false);
                    } else {
                        window.location.reload();
                    }
                } else if (response.errors) {
                    for (var field in response.errors) {
                        var fieldElement = document.getElementById('edit_' + field);
                        if (fieldElement) {
                            var helpBlock = fieldElement.closest('.form-group').querySelector('.help-block');
                            if (helpBlock) {
                                helpBlock.textContent = response.errors[field][0];
                            }
                        }
                    }
                    toastr.error('Please correct the errors');
                } else {
                    toastr.error(response.message || 'Error updating rule');
                }
            },
            error: function (xhr, status, error) {
                btnUpdate.innerHTML = originalHtml;
                btnUpdate.disabled = false;

                var errorMessage = 'Error updating rule';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    for (var field in xhr.responseJSON.errors) {
                        var fieldElement = document.getElementById('edit_' + field);
                        if (fieldElement) {
                            var helpBlock = fieldElement.closest('.form-group').querySelector('.help-block');
                            if (helpBlock) {
                                helpBlock.textContent = xhr.responseJSON.errors[field][0];
                            }
                        }
                    }
                    errorMessage = 'Please correct validation errors';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                toastr.error(errorMessage);
            }
        });
    }

    function populateEditModal(data) {
        editRequiredGroupsCounter = 0;
        editForbiddenGroupsCounter = 0;
        editConstraintsCounter = 0;
        editIncompatiblePairsCounter = 0;

        document.getElementById('edit_rule_id').value = data.rule.id;
        document.getElementById('edit_rule_name').value = data.rule.rule_name;
        document.getElementById('edit_is_active').checked = data.rule.is_active == 1;
        document.getElementById('edit_type').value = data.rule.type;

        var levelOptions = '<option value="">Select Level</option>';
        data.levels.forEach(function (level) {
            var selected = level.id === data.rule.level_id ? 'selected' : '';
            levelOptions += '<option value="' + level.id + '" ' + selected + '>' + level.level + '</option>';
        });
        document.getElementById('edit_level_id').innerHTML = levelOptions;

        editAvailableGroups = data.groups;
        editExistingRules = data.rule.rules || {};

        document.getElementById('edit_min_subjects').value = editExistingRules.min_subjects || '';
        document.getElementById('edit_max_subjects').value = editExistingRules.max_subjects || '';

        displayEditAvailableGroups();
        document.getElementById('edit-required-groups-container').innerHTML = '';
        document.getElementById('edit-forbidden-groups-container').innerHTML = '';
        document.getElementById('edit-constraints-container').innerHTML = '';
        document.getElementById('edit-incompatible-pairs-container').innerHTML = '';

        var helpBlocks = document.querySelectorAll('#editRuleForm .help-block');
        helpBlocks.forEach(function (block) {
            block.textContent = '';
        });

        setTimeout(function () {
            loadEditExistingRules();
            updateEditJsonPreview();
        }, 100);
    }

    function displayEditAvailableGroups() {
        var container = document.getElementById('edit-available-groups-container');
        if (editAvailableGroups.length === 0) {
            container.innerHTML = '<div class="alert alert-warning">No subject groups found for this level.</div>';
            return;
        }

        var html = '<table class="table table-sm table-bordered"><thead><tr><th>Group Code</th><th>Group Name</th><th>Subjects</th></tr></thead><tbody>';
        editAvailableGroups.forEach(function (group) {
            var subjects = group.subjects.map(s => s.subject_code + ' - ' + s.subject_name).join(', ');
            html += '<tr><td><strong>' + group.group_code + '</strong></td><td>' + group.group_name + '</td><td><small>' + subjects + '</small></td></tr>';
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    }

    function loadEditExistingRules() {
        if (editExistingRules.required_groups && Array.isArray(editExistingRules.required_groups)) {
            editExistingRules.required_groups.forEach(function (group) {
                addEditRequiredGroup(group);
            });
        }
        if (editExistingRules.forbidden_groups && Array.isArray(editExistingRules.forbidden_groups)) {
            editExistingRules.forbidden_groups.forEach(function (groupCode) {
                addEditForbiddenGroup(groupCode);
            });
        }
        if (editExistingRules.group_constraints && Array.isArray(editExistingRules.group_constraints)) {
            editExistingRules.group_constraints.forEach(function (constraint) {
                addEditConstraint(constraint);
            });
        }

        if (editExistingRules.incompatible_pairs && Array.isArray(editExistingRules.incompatible_pairs)) {
            editExistingRules.incompatible_pairs.forEach(function (pair) {
                addEditIncompatiblePair(
                    pair.subject_a || pair[0] || '',
                    pair.subject_b || pair[1] || '',
                    pair.message || ''
                );
            });
        }
    }

    function addEditRequiredGroup(existingData) {
        if (editAvailableGroups.length === 0) {
            toastr.warning('No available groups loaded');
            return;
        }

        existingData = existingData || null;
        var id = 'edit-req-' + (++editRequiredGroupsCounter);
        var groupOptions = '<option value="">Select Group</option>';
        editAvailableGroups.forEach(function (group) {
            var selected = existingData && existingData.group_code === group.group_code ? 'selected' : '';
            groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
        });
        var minCount = existingData && existingData.min_count ? existingData.min_count : 1;
        var maxCount = existingData && existingData.max_count ? existingData.max_count : '';
        var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
            '<div class="row">' +
            '<div class="col-md-4">' +
            '<label>Group</label>' +
            '<select class="form-control rule-input edit-required-group-code">' + groupOptions + '</select>' +
            '</div>' +
            '<div class="col-md-4">' +
            '<label>Min Count</label>' +
            '<input type="number" class="form-control rule-input edit-required-group-min" min="1" value="' + minCount + '">' +
            '</div>' +
            '<div class="col-md-3">' +
            '<label>Max Count (Optional)</label>' +
            '<input type="number" class="form-control rule-input edit-required-group-max" min="1" value="' + maxCount + '" placeholder="No limit">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label>Remove</label>' +
            '<button type="button" class="btn btn-xs btn-danger" style="display:block" onclick="document.getElementById(\'' + id + '\').remove(); updateEditJsonPreview();"><i class="fa fa-trash"></i></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        document.getElementById('edit-required-groups-container').insertAdjacentHTML('beforeend', html);
        updateEditJsonPreview();
    }

    function addEditForbiddenGroup(existingGroupCode) {
        if (editAvailableGroups.length === 0) {
            toastr.warning('No available groups loaded');
            return;
        }

        existingGroupCode = existingGroupCode || null;
        var id = 'edit-forb-' + (++editForbiddenGroupsCounter);
        var groupOptions = '<option value="">Select Group</option>';
        editAvailableGroups.forEach(function (group) {
            var selected = existingGroupCode === group.group_code ? 'selected' : '';
            groupOptions += '<option value="' + group.group_code + '" ' + selected + '>' + group.group_code + ' - ' + group.group_name + '</option>';
        });
        var html = '<div class="well well-sm" id="' + id + '" style="margin-bottom: 10px;">' +
            '<div class="row">' +
            '<div class="col-md-11">' +
            '<label>Group</label>' +
            '<select class="form-control rule-input edit-forbidden-group-code">' + groupOptions + '</select>' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label>Remove</label>' +
            '<button type="button" class="btn btn-xs btn-danger pull-right" onclick="document.getElementById(\'' + id + '\').remove(); updateEditJsonPreview();"><i class="fa fa-trash"></i></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        document.getElementById('edit-forbidden-groups-container').insertAdjacentHTML('beforeend', html);
        updateEditJsonPreview();
    }

    function addEditConstraint(existingData) {
        if (editAvailableGroups.length === 0) {
            toastr.warning('No available groups loaded');
            return;
        }

        existingData = existingData || null;
        var id = 'edit-const-' + (++editConstraintsCounter);
        var selectedType = existingData ? existingData.type : '';
        var message = existingData && existingData.message ? existingData.message : '';

        var typeOptions =
            '<option value="">Select Type</option>' +
            '<option value="at_least_one_from_multiple" ' + (selectedType === 'at_least_one_from_multiple' ? 'selected' : '') + '>At Least One From Multiple</option>' +
            '<option value="mutually_exclusive" ' + (selectedType === 'mutually_exclusive' ? 'selected' : '') + '>Mutually Exclusive</option>' +
            '<option value="conditional_required" ' + (selectedType === 'conditional_required' ? 'selected' : '') + '>Conditional Required</option>' +
            '<option value="min_total_from_groups" ' + (selectedType === 'min_total_from_groups' ? 'selected' : '') + '>Min Total From Groups</option>';

        var html =
            '<div class="panel panel-default" id="' + id + '">' +
            '<div class="panel-heading">Constraint ' + editConstraintsCounter + '</div>' +
            '<div class="panel-body">' +
            '<div class="form-group">' +
            '<div class="row">' +
            '<div class="col-md-11">' +
            '<label>Constraint Type</label>' +
            '<select class="form-control rule-input edit-constraint-type" onchange="updateEditConstraintFields(this)">' + typeOptions + '</select>' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label>Remove</label>' +
            '<button type="button" class="btn btn-xs btn-danger" style="display:block" onclick="document.getElementById(\'' + id + '\').remove(); updateEditJsonPreview();"><i class="fa fa-trash"></i></button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="edit-constraint-fields"></div>' +
            '<div class="form-group">' +
            '<label>Custom Message (Optional)</label>' +
            '<input type="text" class="form-control rule-input edit-constraint-message" value="' + message + '" placeholder="Error message">' +
            '</div>' +
            '</div>' +
            '</div>';

        document.getElementById('edit-constraints-container').insertAdjacentHTML('beforeend', html);

        // If loading existing data, populate constraint-specific fields after DOM is ready
        if (existingData && selectedType) {
            setTimeout(function () {
                var panel = document.getElementById(id);
                var select = panel.querySelector('.edit-constraint-type');
                updateEditConstraintFields(select, existingData);
            }, 50);
        }

        updateEditJsonPreview();
    }

    /**
     * Renders the type-specific fields inside a constraint panel.
     * Called both on manual <select> change and when loading existing data.
     *
     * @param {HTMLSelectElement} selectEl  - The constraint type <select>
     * @param {object|null}       existingData - Pre-existing constraint data (optional)
     */
    function updateEditConstraintFields(selectEl, existingData) {
        existingData = existingData || null;
        var type = selectEl.value;
        var panel = selectEl.closest('.panel-body');
        var fieldsContainer = panel.querySelector('.edit-constraint-fields');
        fieldsContainer.innerHTML = '';

        if (!type) {
            updateEditJsonPreview();
            return;
        }

        // Build multi-select group options
        var groupOptions = '<option value="">Select Groups</option>';
        editAvailableGroups.forEach(function (group) {
            groupOptions += '<option value="' + group.group_code + '">' + group.group_code + ' - ' + group.group_name + '</option>';
        });

        var html = '';

        switch (type) {

            case 'at_least_one_from_multiple':
            case 'mutually_exclusive':
                var selectedGroups = (existingData && existingData.groups) ? existingData.groups : [];
                html =
                    '<div class="form-group">' +
                    '<label>Groups (hold Ctrl/Cmd to select multiple)</label>' +
                    '<select class="form-control rule-input edit-constraint-groups" multiple style="height: 120px;">' + groupOptions + '</select>' +
                    '</div>';
                fieldsContainer.innerHTML = html;
                // Pre-select saved groups
                if (selectedGroups.length > 0) {
                    var multiSelect = fieldsContainer.querySelector('.edit-constraint-groups');
                    Array.from(multiSelect.options).forEach(function (opt) {
                        if (selectedGroups.indexOf(opt.value) !== -1) {
                            opt.selected = true;
                        }
                    });
                }
                break;

            case 'conditional_required':
                var ifGroup = (existingData && existingData.if_group) ? existingData.if_group : '';
                var thenGroup = (existingData && existingData.then_group) ? existingData.then_group : '';
                var minCount = (existingData && existingData.min_count) ? existingData.min_count : 1;

                // Build individual selects with pre-selection
                var ifOptions = '<option value="">Select Group</option>';
                var thenOptions = '<option value="">Select Group</option>';
                editAvailableGroups.forEach(function (group) {
                    ifOptions += '<option value="' + group.group_code + '" ' + (ifGroup === group.group_code ? 'selected' : '') + '>' + group.group_code + ' - ' + group.group_name + '</option>';
                    thenOptions += '<option value="' + group.group_code + '" ' + (thenGroup === group.group_code ? 'selected' : '') + '>' + group.group_code + ' - ' + group.group_name + '</option>';
                });

                html =
                    '<div class="row">' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label>If Group Selected</label>' +
                    '<select class="form-control rule-input edit-constraint-if-group">' + ifOptions + '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label>Then Require Group</label>' +
                    '<select class="form-control rule-input edit-constraint-then-group">' + thenOptions + '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label>Min Count</label>' +
                    '<input type="number" class="form-control rule-input edit-constraint-min-count" min="1" value="' + minCount + '">' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                fieldsContainer.innerHTML = html;
                break;

            case 'min_total_from_groups':
                var selectedGroups = (existingData && existingData.groups) ? existingData.groups : [];
                var minTotal = (existingData && existingData.min_total) ? existingData.min_total : 1;

                html =
                    '<div class="row">' +
                    '<div class="col-md-8">' +
                    '<div class="form-group">' +
                    '<label>Groups (hold Ctrl/Cmd to select multiple)</label>' +
                    '<select class="form-control rule-input edit-constraint-groups" multiple style="height: 120px;">' + groupOptions + '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label>Min Total Subjects</label>' +
                    '<input type="number" class="form-control rule-input edit-constraint-min-total" min="1" value="' + minTotal + '">' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                fieldsContainer.innerHTML = html;
                // Pre-select saved groups
                if (selectedGroups.length > 0) {
                    var multiSelect = fieldsContainer.querySelector('.edit-constraint-groups');
                    Array.from(multiSelect.options).forEach(function (opt) {
                        if (selectedGroups.indexOf(opt.value) !== -1) {
                            opt.selected = true;
                        }
                    });
                }
                break;
        }

        updateEditJsonPreview();
    }

    function getEditSubjectOptionsFromGroups(selectedCode) {
        var seen = {}, subjects = [];
        editAvailableGroups.forEach(function (group) {
            (group.subjects || []).forEach(function (s) {
                if (!seen[s.subject_code]) { seen[s.subject_code] = true; subjects.push(s); }
            });
        });
        subjects.sort(function (a, b) { return a.subject_code.localeCompare(b.subject_code); });
        var options = '<option value="">Select Subject</option>';
        subjects.forEach(function (s) {
            var sel = (selectedCode === s.subject_code) ? 'selected' : '';
            options += '<option value="' + s.subject_code + '" ' + sel + '>' + s.subject_code + ' - ' + s.subject_name + '</option>';
        });
        return options;
    }

    function addEditIncompatiblePair(existingA, existingB, existingMsg) {
        existingA = existingA || '';
        existingB = existingB || '';
        existingMsg = existingMsg || '';

        var id = 'edit-pair-' + (++editIncompatiblePairsCounter);
        var html =
            '<div class="well well-sm" id="' + id + '" style="margin-bottom:10px;">' +
            '<div class="row">' +
            '<div class="col-md-4">' +
            '<label>Subject A</label>' +
            '<select class="form-control rule-input edit-incompatible-pair-a">' + getEditSubjectOptionsFromGroups(existingA) + '</select>' +
            '</div>' +
            '<div class="col-md-4">' +
            '<label>Subject B</label>' +
            '<select class="form-control rule-input edit-incompatible-pair-b">' + getEditSubjectOptionsFromGroups(existingB) + '</select>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<label>Custom Message <small class="text-muted">(optional)</small></label>' +
            '<input type="text" class="form-control rule-input edit-incompatible-pair-msg" placeholder="Cannot combine these subjects" value="' + existingMsg + '">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label>Remove</label>' +
            '<button type="button" class="btn btn-xs btn-danger" style="display:block" ' +
            'onclick="document.getElementById(\'' + id + '\').remove(); updateEditJsonPreview();">' +
            '<i class="fa fa-trash"></i>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';
        document.getElementById('edit-incompatible-pairs-container').insertAdjacentHTML('beforeend', html);
        updateEditJsonPreview();
    }

    function buildEditRulesJson() {
        var rules = {};
        var minSubjects = parseInt(document.getElementById('edit_min_subjects').value);
        var maxSubjects = parseInt(document.getElementById('edit_max_subjects').value);
        if (minSubjects) rules.min_subjects = minSubjects;
        if (maxSubjects) rules.max_subjects = maxSubjects;

        rules.required_groups = [];
        var reqGroups = document.querySelectorAll('.edit-required-group-code');
        reqGroups.forEach(function (select) {
            var groupCode = select.value;
            if (groupCode) {
                var well = select.closest('.well');
                var minCount = parseInt(well.querySelector('.edit-required-group-min').value) || 1;
                var maxCount = parseInt(well.querySelector('.edit-required-group-max').value);
                var group = { group_code: groupCode, min_count: minCount };
                if (maxCount) group.max_count = maxCount;
                rules.required_groups.push(group);
            }
        });

        rules.forbidden_groups = [];
        var forbGroups = document.querySelectorAll('.edit-forbidden-group-code');
        forbGroups.forEach(function (select) {
            var groupCode = select.value;
            if (groupCode) rules.forbidden_groups.push(groupCode);
        });

        rules.group_constraints = [];
        var constTypes = document.querySelectorAll('.edit-constraint-type');
        constTypes.forEach(function (select) {
            var type = select.value;
            if (!type) return;
            var panel = select.closest('.panel-body');
            var constraint = { type: type };
            var messageEl = panel.querySelector('.edit-constraint-message');
            if (messageEl && messageEl.value) constraint.message = messageEl.value;

            switch (type) {
                case 'at_least_one_from_multiple':
                case 'mutually_exclusive':
                    var groupsSelect = panel.querySelector('.edit-constraint-groups');
                    if (groupsSelect) {
                        var groups = Array.from(groupsSelect.selectedOptions).map(function (opt) { return opt.value; });
                        if (groups.length > 0) {
                            constraint.groups = groups;
                            rules.group_constraints.push(constraint);
                        }
                    }
                    break;
                case 'conditional_required':
                    var ifGroupEl = panel.querySelector('.edit-constraint-if-group');
                    var thenGroupEl = panel.querySelector('.edit-constraint-then-group');
                    var minCountEl = panel.querySelector('.edit-constraint-min-count');
                    if (ifGroupEl && thenGroupEl && ifGroupEl.value && thenGroupEl.value) {
                        constraint.if_group = ifGroupEl.value;
                        constraint.then_group = thenGroupEl.value;
                        constraint.min_count = parseInt(minCountEl.value) || 1;
                        rules.group_constraints.push(constraint);
                    }
                    break;
                case 'min_total_from_groups':
                    var groupsSelect = panel.querySelector('.edit-constraint-groups');
                    var minTotalEl = panel.querySelector('.edit-constraint-min-total');
                    if (groupsSelect) {
                        var groups = Array.from(groupsSelect.selectedOptions).map(function (opt) { return opt.value; });
                        if (groups.length > 0) {
                            constraint.groups = groups;
                            constraint.min_total = parseInt(minTotalEl.value) || 1;
                            rules.group_constraints.push(constraint);
                        }
                    }
                    break;
            }
        });

        rules.incompatible_pairs = [];
        document.querySelectorAll('#edit-incompatible-pairs-container .well').forEach(function (well) {
            var a = well.querySelector('.edit-incompatible-pair-a').value;
            var b = well.querySelector('.edit-incompatible-pair-b').value;
            var msg = well.querySelector('.edit-incompatible-pair-msg').value.trim();
            if (a && b && a !== b) {
                var pair = { subject_a: a, subject_b: b };
                if (msg) pair.message = msg;
                rules.incompatible_pairs.push(pair);
            }
        });

        return rules;
    }

    function updateEditJsonPreview() {
        var rules = buildEditRulesJson();
        document.getElementById('edit-json-preview').textContent = JSON.stringify(rules, null, 2);
    }

    function toggleEditJsonPreview() {
        var container = document.getElementById('edit-json-preview-container');
        if (container.style.display === 'none') {
            container.style.display = 'block';
            updateEditJsonPreview();
        } 
        else {
            container.style.display = 'none';
        }
    }

    // Event delegation for dynamically added elements
    $(document).ready(function () {
        $(document).on('change', '#editRuleModal .rule-input', function () {
            updateEditJsonPreview();
        });
    });
</script>