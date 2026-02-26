@foreach ($serviceAttributes as $attribute)
    @php
        $inputId = 'attr_' . $attribute->code;
        $inputName = $attribute->code;
        $isRequired = $attribute->is_required ?? false;
        $label = $attribute->label ?? ucfirst(str_replace('_', ' ', $attribute->code));
    @endphp

    <div class="form__field mb-3">
        <label for="{{ $inputId }}">
            {{ $label }}
            @if ($isRequired)
                <span data-required="true" aria-hidden="true"></span>
            @endif
        </label>

        @switch($attribute->frontend_type)
            {{-- Center Field (with Select2 AJAX autocomplete) --}}
            @case('center')
                <select 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-select livesearch-all-centers"
                    @if ($isRequired) required @endif
                >
                    <option value="">-- Select {{ $label }} --</option>
                </select>
                @break

            {{-- Level Dropdown --}}
            @case('level')
                <select 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-select"
                    @if ($isRequired) required @endif
                >
                    <option value="">-- Select {{ $label }} --</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" data-level="{{ $level->level ?? $level->name }}">
                            {{ $level->name ?? $level->level }}
                        </option>
                    @endforeach
                </select>
                @break

            {{-- Text Input --}}
            @case('text')
                <input 
                    type="text" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    placeholder="{{ $attribute->placeholder ?? '' }}"
                    value="{{ old($inputName, $attribute->default_value ?? '') }}"
                    @if ($isRequired) required @endif
                >
                @break

            {{-- Email Input --}}
            @case('email')
                <input 
                    type="email" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    placeholder="{{ $attribute->placeholder ?? '' }}"
                    value="{{ old($inputName, $attribute->default_value ?? '') }}"
                    @if ($isRequired) required @endif
                >
                @break

            {{-- Number Input --}}
            @case('number')
                <input 
                    type="number" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    placeholder="{{ $attribute->placeholder ?? '' }}"
                    value="{{ old($inputName, $attribute->default_value ?? '') }}"
                    @if ($isRequired) required @endif
                >
                @break

            {{-- Date Input --}}
            @case('date')
                <input 
                    type="date" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    value="{{ old($inputName, $attribute->default_value ?? '') }}"
                    @if ($isRequired) required @endif
                >
                @break

            {{-- Select Dropdown --}}
            @case('select')
                @php
                    $isCenterField = isset($attribute->code) && strtolower($attribute->code) === 'center';
                    $isLevelField = isset($attribute->code) && strtolower($attribute->code) === 'level';
                @endphp

                @if($isCenterField)
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select livesearch-all-centers"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select {{ $label }} --</option>
                    </select>
                @elseif($isLevelField)
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select {{ $label }} --</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name ?? $level->level }}</option>
                        @endforeach
                    </select>
                @else
                    <select 
                        id="{{ $inputId }}" 
                        name="{{ $inputName }}" 
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select {{ $label }} --</option>
                        @php
                            $options = json_decode($attribute->options ?? '[]', true);
                            if (is_string($attribute->options)) {
                                $options = explode(',', $attribute->options);
                                $options = array_map('trim', $options);
                            }
                            $options = $options ?? [];
                        @endphp
                        @foreach ($options as $option)
                            <option value="{{ $option }}" @if (old($inputName) == $option) selected @endif>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @break

            {{-- Textarea --}}
            @case('textarea')
                <textarea 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    rows="4"
                    placeholder="{{ $attribute->placeholder ?? '' }}"
                    @if ($isRequired) required @endif
                >{{ old($inputName, $attribute->default_value ?? '') }}</textarea>
                @break

            {{-- File Upload --}}
            @case('file')
                <input 
                    type="file" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    accept="{{ $attribute->file_types ?? '.pdf,.jpg,.jpeg,.png' }}"
                    @if ($isRequired) required @endif
                >
                @if ($attribute->help_text)
                    <small class="text-muted d-block mt-2">{{ $attribute->help_text }}</small>
                @endif
                @break

            {{-- Checkbox --}}
            @case('checkbox')
                <div class="form-check mt-2">
                    <input 
                        type="checkbox" 
                        id="{{ $inputId }}" 
                        name="{{ $inputName }}" 
                        class="form-check-input"
                        value="1"
                        @if (old($inputName)) checked @endif
                    >
                    <label class="form-check-label" for="{{ $inputId }}">
                        {{ $attribute->help_text ?? $label }}
                    </label>
                </div>
                @break

            {{-- Default: Text Input --}}
            @default
                <input 
                    type="text" 
                    id="{{ $inputId }}" 
                    name="{{ $inputName }}" 
                    class="form-control"
                    placeholder="{{ $attribute->placeholder ?? '' }}"
                    value="{{ old($inputName, $attribute->default_value ?? '') }}"
                    @if ($isRequired) required @endif
                >
        @endswitch

        @error($inputName)
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror

        {{-- Subjects Selection Panel (shows after center field) --}}
        @if($attribute->frontend_type === 'center')
            <div class="subjects_selection mt-3"></div>
        @endif
    </div>
@endforeach
