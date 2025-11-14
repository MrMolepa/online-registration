<!-- resources/views/workflows/_form.blade.php -->
@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Workflow Name *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
               id="name" name="name"
               value="{{ old('name', $workflow->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="entity_type" class="form-label">Entity Type *</label>
        <select class="form-select select2-entities @error('entity_type') is-invalid @enderror"
                id="entity_type" name="entity_type" required
                data-placeholder="Select entity type">
            <option value="">Select Entity Type</option>
            @foreach($entityTypes as $key => $label)
                <option value="{{ $key }}"
                    {{ old('entity_type', $workflow->entity_type ?? '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('entity_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror"
              id="description" name="description"
              rows="2">{{ old('description', $workflow->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(isset($workflow) && $workflow->exists)
<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
           value="1" {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>
@endif

<hr class="my-4">

<h5>Workflow Steps</h5>
<div id="steps-container">
    @if(isset($workflow) && $workflow->steps->isNotEmpty())
        @foreach($workflow->steps as $index => $step)
            <div class="step-item card mb-3" data-index="{{ $index }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Step Name *</label>
                            <input type="text" class="form-control"
                                   name="steps[{{ $index }}][name]"
                                   value="{{ $step->name }}" required>
                            <input type="hidden" name="steps[{{ $index }}][id]" value="{{ $step->id }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assign Entities *</label>
                            <select class="form-select select2-entities"
                                    name="steps[{{ $index }}][entity_ids][]"
                                    multiple="multiple" required>
                                @foreach($roles as $id => $name)
                                    <option value="role_{{ $id }}"
                                        {{ in_array($id, $step->roles->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        Role: {{ $name }}
                                    </option>
                                @endforeach
                                @foreach($users as $id => $name)
                                    <option value="user_{{ $id }}"
                                        {{ in_array($id, $step->users->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        User: {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1"><strong>Mandatory</strong></div>
                            <div class="mb-3 mt-3">
                                <span class="btn btn-outline-primary btn-sm px-0 py-1 d-inline-flex align-items-center justify-content-center" style="min-width:28px;">
                                    <input class="form-check-input me-0" type="checkbox"
                                           name="steps[{{ $index }}][is_mandatory]" value="1"
                                           id="mandatory-{{ $index }}"
                                           {{ $step->is_mandatory ? 'checked' : '' }}>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm px-2 py-1 shadow-sm remove-step"
                                    {{ $workflow->steps->count() <= 1 ? 'disabled' : '' }}
                                    title="Remove step" aria-label="Remove step" style="min-width:40px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="step-item card mb-3" data-index="0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Step Name *</label>
                        <input type="text" class="form-control" name="steps[0][name]" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Assign Entities *</label>
                        <select class="form-select select2-entities"
                                name="steps[0][entity_ids][]"
                                multiple="multiple" required>
                            @foreach($roles as $id => $name)
                                <option value="role_{{ $id }}">Role: {{ $name }}</option>
                            @endforeach
                            @foreach($users as $id => $name)
                                <option value="user_{{ $id }}">User: {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1"><strong>Mandatory</strong></div>
                        <div class="mb-3 mt-3">
                            <span class="btn btn-outline-primary btn-sm px-0 py-1 d-inline-flex align-items-center justify-content-center" style="min-width:28px;">
                                <input class="form-check-input me-0" type="checkbox"
                                       name="steps[0][is_mandatory]" value="1"
                                       id="mandatory-0" checked>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm px-2 py-1 shadow-sm remove-step" disabled
                                title="Remove step" aria-label="Remove step" style="min-width:40px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="mb-4">
    <button type="button" class="btn btn-outline-primary" id="add-step">
        <i class="fas fa-plus me-1"></i> Add Step
    </button>
</div>

<!-- Template for new steps (uses placeholder __INDEX__ that JS will replace) -->
<template id="step-template">
    <div class="step-item card mb-3" data-index="__INDEX__">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Step Name *</label>
                    <input type="text" class="form-control" name="steps[__INDEX__][name]" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assign Entities *</label>
                    <select class="form-select select2-entities" name="steps[__INDEX__][entity_ids][]" multiple="multiple" required>
                        @foreach($roles as $id => $name)
                            <option value="role_{{ $id }}">Role: {{ $name }}</option>
                        @endforeach
                        @foreach($users as $id => $name)
                            <option value="user_{{ $id }}">User: {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="mb-1"><strong>Mandatory</strong></div>
                    <div class="mb-3 mt-3">
                        <span class="btn btn-outline-primary btn-sm px-0 py-1 d-inline-flex align-items-center justify-content-center" style="min-width:28px;">
                            <input class="form-check-input me-0" type="checkbox" name="steps[__INDEX__][is_mandatory]" value="1" id="mandatory-__INDEX__" checked>
                        </span>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm px-2 py-1 shadow-sm remove-step"
                            title="Remove step" aria-label="Remove step" style="min-width:40px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>



@push('scripts')
<script>
(function($){
    // Helper to initialize select2 if available
    function initSelect2(el){
        if ($.fn.select2) {
            var $el = $(el);
            var placeholder = $el.data('placeholder') || 'Select entities';
            $el.select2({
                placeholder: placeholder,
                width: '100%'
            });
        }
    }

    // Reindex all steps so their input names and ids are sequential (0..n-1)
    function reindexSteps(){
        var $container = $('#steps-container');
        var $items = $container.find('.step-item');

        $items.each(function(idx, card){
            var $card = $(card);
            $card.attr('data-index', idx);

            // Update inputs/selects with name attributes
            $card.find('[name]').each(function(_, el){
                el.name = el.name.replace(/steps\[\d+\]/, `steps[${idx}]`);
            });

            // Update any ids that include the index (e.g., mandatory-__INDEX__)
            $card.find('[id]').each(function(_, el){
                if (el.id.match(/-\d+$/)){
                    var base = el.id.replace(/-\d+$/, '');
                    el.id = base + '-' + idx;
                }
            });

            // Update labels 'for' attributes
            $card.find('label[for]').each(function(_, lb){
                if (lb.htmlFor.match(/-\d+$/)){
                    var base = lb.htmlFor.replace(/-\d+$/, '');
                    lb.htmlFor = base + '-' + idx;
                }
            });
        });

        // Enable/disable remove buttons: require at least one step
        var $removeButtons = $container.find('.remove-step');
        if ($removeButtons.length <= 1){
            $removeButtons.attr('disabled', true);
        } else {
            $removeButtons.removeAttr('disabled');
        }
    }

    function addStep(){
        var tpl = $('#step-template').html();
        var $container = $('#steps-container');
        var index = $container.find('.step-item').length;

        var html = tpl.replace(/__INDEX__/g, index);
        $container.append(html);

        var $newItem = $container.children().last();

        // initialize select2 on new select
        var $sel = $newItem.find('.select2-entities');
        if ($sel.length) initSelect2($sel);

        reindexSteps();
    }

    // Event delegation for remove buttons
    $(document).on('click', '.remove-step', function(e){
        var $card = $(this).closest('.step-item');
        if ($card.length) {
            $card.remove();
            reindexSteps();
        }
    });

    // Expose an initializer so AJAX-loaded forms can call it after insertion
    window.initWorkflowForm = function(){
        var $addBtn = $('#add-step');
        $addBtn.off('click').on('click', addStep);

        // initialize existing select2 elements
        $('.select2-entities').each(function(_, s){
            initSelect2(s);
        });

        // ensure remove buttons state is correct on load
        reindexSteps();
    };

    // Run initializer on initial page load for create modal
    $(function(){
        if (typeof window.initWorkflowForm === 'function') {
            window.initWorkflowForm();
        }
    });
})(jQuery);

</script>
@endpush
