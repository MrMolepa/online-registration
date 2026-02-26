@foreach ($serviceAttributes as $attribute)
    @php
        $inputId    = 'attr_' . $attribute->code;
        $inputName  = $attribute->code;
        $isRequired = $attribute->is_required ?? false;
        $label      = $attribute->label ?? ucfirst(str_replace('_', ' ', $attribute->code));
    @endphp

    <div class="form__field mb-3">
        <label for="{{ $inputId }}">
            {{ $label }}
            @if ($isRequired)
                <span data-required="true" aria-hidden="true"></span>
            @endif
        </label>

        @switch($attribute->frontend_type)

            {{-- ── Centre: Select2 AJAX autocomplete ──────────────────────────── --}}
            @case('center')
                <select
                    id="{{ $inputId }}"
                    name="{{ $inputName }}"
                    class="form-select livesearch-all-centers"
                    @if ($isRequired) required @endif
                >
                    <option value="">Select the Center</option>
                </select>
                <div class="subjects_selection mt-3"></div>
                @break

            {{-- ── Level: id="level" + data-level for JS subjects AJAX ─────────── --}}
            @case('level')
                <select
                    id="level"
                    name="{{ $inputName }}"
                    class="form-select"
                    @if ($isRequired) required @endif
                >
                    <option value="">-- Select {{ $label }} --</option>
                    @foreach ($levels as $level)
                        <option
                            value="{{ $level->id }}"
                            data-level="{{ $level->level }}"
                            @if (old($inputName) == $level->id) selected @endif
                        >
                            {{ $level->level }}
                            @if ($level->description) – {{ $level->description }} @endif
                        </option>
                    @endforeach
                </select>
                @break

            {{-- ── Text ──────────────────────────────────────────────────────────── --}}
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

            {{-- ── Email ────────────────────────────────────────────────────────── --}}
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

            {{-- ── Number ───────────────────────────────────────────────────────── --}}
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

            {{-- ── Date ────────────────────────────────────────────────────────── --}}
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

            {{-- ── Select ───────────────────────────────────────────────────────── --}}
            @case('select')
                @php $code = strtolower($attribute->code); @endphp

                @if ($code === 'center')
                    {{-- Centre via Select2 AJAX --}}
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select livesearch-all-centers"
                        @if ($isRequired) required @endif
                    >
                        <option value="">Select the Center</option>
                    </select>
                    <div class="subjects_selection mt-3"></div>

                @elseif ($code === 'level')
                    {{-- Level from $levels --}}
                    <select
                        id="level"
                        name="{{ $inputName }}"
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select {{ $label }} --</option>
                        @foreach ($levels as $level)
                            <option
                                value="{{ $level->id }}"
                                data-level="{{ $level->level }}"
                                @if (old($inputName) == $level->id) selected @endif
                            >
                                {{ $level->level }}
                                @if ($level->description) – {{ $level->description }} @endif
                            </option>
                        @endforeach
                    </select>

                @elseif ($code === 'exam_series')
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select Exam Series --</option>
                        <option value="May/June" @if (old($inputName) == 'May/June') selected @endif>May/June</option>
                        <option value="October/November" @if (old($inputName) == 'October/November') selected @endif>October/November</option>
                    </select>

                @elseif ($code === 'year')
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select Year --</option>
                        @foreach (range(date('Y'), 1942) as $year)
                            <option
                                value="{{ $year }}"
                                @if (old($inputName) == $year) selected @endif
                            >
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                @else
                    {{-- Generic select: options stored as JSON or CSV --}}
                    @php
                        $options = is_string($attribute->options)
                            ? (json_decode($attribute->options, true) ?? array_map('trim', explode(',', $attribute->options)))
                            : ($attribute->options ?? []);
                    @endphp
                    <select
                        id="{{ $inputId }}"
                        name="{{ $inputName }}"
                        class="form-select"
                        @if ($isRequired) required @endif
                    >
                        <option value="">-- Select {{ $label }} --</option>
                        @foreach ($options as $option)
                            <option value="{{ $option }}" @if (old($inputName) == $option) selected @endif>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @break

            {{-- ── Textarea ─────────────────────────────────────────────────────── --}}
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

            {{-- ── File upload ──────────────────────────────────────────────────── --}}
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

            {{-- ── Checkbox ─────────────────────────────────────────────────────── --}}
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

            {{-- ── Default fallback ─────────────────────────────────────────────── --}}
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
    </div>
@endforeach