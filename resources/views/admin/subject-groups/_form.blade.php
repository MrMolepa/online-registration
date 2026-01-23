<!-- Add Subject Group Modal -->
<div class="modal fade" id="add-group-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Subject Group</h4>
            </div>
            <form id="addGroupForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Group Code *</label>
                        <input type="text" name="group_code" class="form-control">
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Group Name *</label>
                        <input type="text" name="group_name" class="form-control">
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level_id" class="form-control">
                            <option value="">Select Level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->level }}</option>
                            @endforeach
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Subjects *</label>
                        <select name="subjects[]" id="subjects_select" class="form-control" multiple="multiple"
                            style="width: 100%;">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->subject_code }}">{{ $subject->subject_code }} -
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" value="1" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="save-group">
                        Save
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Group Modal -->
<div class="modal fade" id="edit-group-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Subject Group</h4>
            </div>
            <form id="editGroupForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_group_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Group Code *</label>
                        <input type="text" name="group_code" id="edit_group_code" class="form-control">
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Group Name *</label>
                        <input type="text" name="group_name" id="edit_group_name" class="form-control">
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Level *</label>
                        <select name="level_id" id="edit_level_id" class="form-control">
                            <option value="">Select Level</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->level }}</option>
                            @endforeach
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Subjects *</label>
                        <select name="subjects[]" id="edit_subjects_select" class="form-control" multiple="multiple"
                            style="width: 100%;">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->subject_code }}">{{ $subject->subject_code }} -
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="update-group">
                        <i class="fa fa-save"></i> Update
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>