<?php

namespace App\Imports;

use App\Models\CenterCandidate;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class CenterCandidateImport implements ToModel, SkipsOnFailure, WithValidation,WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    use Importable, SkipsFailures;
    public function model(array $row)
    {
        return new CenterCandidate([
            'candidate_no' => $row[1],
            'center_no' => $row[0],
            'type' => $row[5],
            'session' => 'November',
            'subject_number' => $row[10],
            'sponser' => $row[20],
        ]);
    }


    public function rules(): array
    {

        for($x=0; $x<=14; $x++) {
            $validate_array['radio_'. $x] = 'required';
        }
        return [
            "*.0" => ['required'],
            "*.1" => ['required', 'exists:candidates,candidate_no',],
            "*.2" => ['required', 'exists:candidates,candidate_surname'],
            "*.5" => ['required'],
            "*.10" => ['required'],
        ];
    }

      /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }


    public function customValidationAttributes()
    {
        return ['1.candidate_no' => 'candidate_no'];
    }
}
