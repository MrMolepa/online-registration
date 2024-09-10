@php
    function filterable($name, $serviceAttribute, $levels = null)
    {
        switch ($name) {
            case 'candidate_no':
                $input = "<div class='form__field'>
                            <label for='$serviceAttribute->code'>
                                $serviceAttribute->name
                                <span data-required='true' aria-hidden='true'></span>
                            </label>
                            <input id='$serviceAttribute->code' type='$serviceAttribute->frontend_type'
                                name='$serviceAttribute->code' placeholder='$serviceAttribute->placeholder'
                                autocomplete='shipping address-line1' required>
                                <input id='is_candidate' type='hidden' name='is_candidate' value=''
                                required>
                        </div>";
                return $input;
            case 'exam_series':
                $input = "<div class='form__field'>
                            <label for='$serviceAttribute->code'>
                                $serviceAttribute->name
                                <span data-required='true' aria-hidden='true'></span>
                            </label>
                            <select id='$serviceAttribute->code' name='$serviceAttribute->code' autocomplete='$serviceAttribute->code' required>
                                <option value='' disabled selected>Please select</option>
                                <option value='June'>May/June</option>
                                <option value='Novemver'>October/November</option>
                            </select>
                         </div>";
                return $input;
                break;
            case 'year':
                $startingYear = date('Y');
                $endingYear = $startingYear - 84;
                $yearHTML = '';
                $years = range($startingYear, $endingYear);
                foreach ($years as $year) {
                    $yearHTML .= "<option value='$year'>$year</option>";
                }
                $input = "<div class='form__field'>
                              <label for='$serviceAttribute->code'>
                                $serviceAttribute->name
                                <span data-required='true' aria-hidden='true'></span>
                            </label>
                            <select id='year' name='$serviceAttribute->code' autocomplete='$serviceAttribute->code' required>
                                <option value='' disabled selected>Please select</option>
                                $yearHTML
                            </select>
                         </div>";
                return $input;
                break;
            case 'subject':
                $input = " <div class='form__field mt-3'>
                                <h2 class='fs-title'>* Subjects to be re-marked</h2>
                         </div>
                        <input id='is_subject' type='hidden' name='is_subject' value='' required>
                         <fieldset id='subjects' class='mt-3 row form__field subjects_selection'>
                        </fieldset>
                          ";
                return $input;
                break;
            case 'level':
                $levelHTML = '';
                foreach ($levels as $level) {
                    $levelHTML .= "<option value='$level->level' data-level='$level->id'>$level->level</option>";
                }
                $input = "<div class='form__field'>
                               <label for='$serviceAttribute->code'>
                                 $serviceAttribute->name
                             <span data-required='true' aria-hidden='true'></span>
                             </label>
                            <select id='level' name='$serviceAttribute->code' autocomplete='$serviceAttribute->code' required>
                                 $levelHTML
                           </select>
                          </div>";
                return $input;
                break;
            case 'center':
                $input = "<div class='form__field'>
                          <label for='$serviceAttribute->code'>
                                $serviceAttribute->name
                                <span data-required='true' aria-hidden='true'></span>
                            </label>
                            <select class='livesearch-all-centers' required name='$serviceAttribute->code'>
                                <option value='' disabled selected>Please select</option>
                            </select>
                         </div>";
                return $input;
                break;
            default:
                break;
        }
    }
@endphp
@foreach ($serviceAttributes as $serviceAttribute)
    @if ($serviceAttribute->is_filterable == 1)
        {!! filterable($serviceAttribute->code, $serviceAttribute, $levels) !!}
    @else
        <div class="form__field">
            <label for="{{ $serviceAttribute->code }}">
                {{ $serviceAttribute->name }}
                <span data-required="true" aria-hidden="true"></span>
            </label>
            <input id="{{ $serviceAttribute->code }}" type="{{ $serviceAttribute->frontend_type }}"
                name="{{ $serviceAttribute->code }}" placeholder="{{ $serviceAttribute->placeholder }}"
                autocomplete="shipping address-line1" required>
        </div>
    @endif
@endforeach
<div class="d-flex  sm:flex-row align-items-center justify-center sm:justify-end mt-4 sm:mt-5">
    <button type="button" class="mt-1 sm:mt-0 button--simple" data-action="prev">
        Back
    </button>
    <button type="button" data-action="next">
        Continue
    </button>
</div>
