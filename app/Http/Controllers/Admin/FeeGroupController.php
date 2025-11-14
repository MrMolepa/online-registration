<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeDetail;
use App\Models\FeeGroup;
use App\Models\FeeLateFrequency;
use App\Models\FeeType;
use App\Models\Level;
use App\Models\Session;
use App\Models\Subject;
use App\Rules\ArrayAtleastOneRequired;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class FeeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //options
        if ($request->ajax()) {
            $feegroups = FeeGroup::with('session', 'level', 'feetypes');
            return DataTables::of($feegroups)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.groups.edit', $row->id)  . '" data-original-title="Edit" class="edit-group   btn btn-primary  btn-sm fa fa-edit"></a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.groups.destroy', $row->id)   . '" data-original-title="Delete" class="delete-group btn btn-danger  btn-sm fa fa-trash"></a>';
                    return $btn;
                })
                ->addColumn('fee_details', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.groups.show', $row->id)  . '" data-original-title="Edit" class="btn-details btn btn-primary fa fa-plus"></a>';
                    return $btn;
                })
                ->editColumn('feetypes', function ($model) {
                    $feetypes = $model->feetypes;
                    $feetypes->map(function ($feetype)  {
                        $fee_types =FeeType::get();
                        $feetypeHtml='';
                        foreach ($fee_types as $fee_type) {
                            if ($feetype->id==$fee_type->id) {
                               $feetypeHtml .="<option value='$fee_type->id' selected>$fee_type->fee_name</option>";
                            }else{
                                $feetypeHtml .="<option value='$fee_type->id'>$fee_type->fee_name</option>";
                            }

                        }
                        $feetype['fee_name']= "<span class='editSpan fee_type_id'>" . $feetype['fee_name'] . " </span>
                            <select class='form-control editInput' id='fee_type_id' name='fee_type_id'>
                                   $feetypeHtml
                            </select>";

                        $feetype['pivot']['amount'] = "<span class='editSpan amount'>" . $feetype['pivot']['amount'] . "</span>
                                                      <input type='hidden' class='editInput' name='_method' value='PUT'>
                                                      <input class='editInput amount form-control' type='text' name='amount' value='" . $feetype['pivot']['amount'] . "'>";
                        $feetype['pivot']['actions'] = "<a href='javascript:void(0)' data-original-title='Edit' class='editBtn btn btn-primary  btn-sm fa fa-edit'></a>
                                                         <a href='javascript:void(0)'  data-url='" . route('admin.fees-stracture.groups.updateDetail', $feetype['pivot']['id'])   . "'  class='saveBtn btn btn-success  btn-sm fa fa-save'></a>
                                                         <a href='javascript:void(0)'  data-url='" . route('admin.fees-stracture.groups.destroyDetail', $feetype['pivot']['id'])   . "'  class='delete-detail btn btn-danger  btn-sm fa fa-trash'></a>
                                                        ";
                        return $feetype;
                    });
                    return   $feetypes;
                })
                ->addColumn('feetypes', function ($row) {
                    return $row->feetypes;
                })

                ->rawColumns(['action', 'fee_details', 'feetypes'])
                ->make(true);
        }
        $sessions = Session::get();
        $levels = Level::get();
        $feetypes = FeeType::get();
        $feegroups=FeeGroup::with("session")->get();
        $frequencies=FeeLateFrequency::get();
        return view('admin.finance.fee-strature.index', compact('sessions', 'levels',
         'feetypes','frequencies','feegroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'session_id' => ['required', Rule::unique('fee_groups')->where(function ($query) use ($request) {
                return $query->where('session_id', $request->session_id)
                    ->where('level_id', $request->level_id)
                    ->where('candidate_type', $request->candidate_type);
            })],
            'level_id' => 'required|string',
            'candidate_type' => 'required|string',
        ]);




        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {

            $feegroup = new FeeGroup();
            $feegroup->name = $request->name;
            $feegroup->description = $request->description;
            $feegroup->session_id = $request->session_id;
            $feegroup->level_id = $request->level_id;
            $feegroup->candidate_type = $request->candidate_type;
            $feegroup->save();
            return response()->json(['success' => "Fee group added successfully"]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feedetails = FeeGroup::find($id);
        $url = route('admin.fees-stracture.groups.updateMultDetails', $id);
        return response()->json(['feedetails' => $feedetails, 'url' => $url]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feegroup = FeeGroup::find($id);

        $url = route('admin.fees-stracture.groups.update', $id);

        return response()->json(['feegroup' => $feegroup, 'url' => $url]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMultDetails(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fee_type_id' => 'required|string',
            'key_type' => 'required|string',
            'feegroup_detail.amount'  => [new ArrayAtleastOneRequired()],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {

            $keyType = $request->key_type;
            $group_id = $id;
            $fee_type = $request->fee_type_id;
            $feegroup = FeeGroup::find($id);
            switch ($keyType) {
                case 'L':
                    $feegroup_details = (object)$request->feegroup_detail;
                    foreach ($feegroup_details->amount as $key => $amount) {
                        $availableChecker = DB::table('fee_group_details')
                            ->select('key_type')
                            ->where('key_type', '=', $keyType)
                            ->where('fee_type_id', '=', $fee_type)
                            ->where('fee_group_id', '=', $group_id)
                            ->first();

                        if ($availableChecker !== null) {
                            DB::table('fee_group_details')
                                ->where('key_type', '=', $keyType)
                                ->where('fee_type_id', '=', $fee_type)
                                ->where('fee_group_id', '=', $group_id)
                                ->update(
                                    [
                                        'amount' => $amount,
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => '-',
                                        'component_code' => '-'
                                    ]
                                );
                        } else {
                            DB::table('fee_group_details')
                                ->insert(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'fee_group_id' => $group_id,
                                        'fee_type_id' => $fee_type,
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => '-',
                                        'component_code' => '-'
                                    ]
                                );
                        }
                    }

                    return response()->json(['success' => 'Fee detail added successfully']);
                    break;
                case 'S':
                    $feegroup_details = (object)$request->feegroup_detail;
                    foreach ($feegroup_details->subject_code as $key => $subject_code) {
                        $availableChecker = DB::table('fee_group_details')
                            ->select('key_type')
                            ->where('subject_code', '=', $subject_code)
                            ->where('key_type', '=', $keyType)
                            ->where('fee_type_id', '=', $fee_type)
                            ->where('fee_group_id', '=', $group_id)
                            ->first();


                        if ($availableChecker !== null) {
                            DB::table('fee_group_details')
                                ->where('subject_code', '=', $subject_code)
                                ->where('key_type', '=', $keyType)
                                ->where('fee_type_id', '=', $fee_type)
                                ->where('fee_group_id', '=', $group_id)->update(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'key_type' => $keyType,
                                        'subject_code' => $subject_code,
                                        'option_code' => '-',
                                        'component_code' => '-'
                                    ]
                                );
                        } else {
                            DB::table('fee_group_details')
                                ->insert(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'fee_group_id' => $group_id,
                                        'fee_type_id' => $fee_type,
                                        'key_type' => $keyType,
                                        'subject_code' => $subject_code,
                                        'option_code' => '-',
                                        'component_code' => '-'
                                    ]
                                );
                        }
                    }

                    return response()->json(['success' => 'Fee detail added successfully']);

                case 'O':

                    $feegroup_details = (object)$request->feegroup_detail;
                    foreach ($feegroup_details->option_code as $key => $option_code) {
                        $availableChecker = DB::table('fee_group_details')
                            ->select('key_type')
                            ->where('option_code', '=', $option_code)
                            ->where('key_type', '=', $keyType)
                            ->where('fee_type_id', '=', $fee_type)
                            ->where('fee_group_id', '=', $group_id)
                            ->first();


                        if ($availableChecker !== null) {
                            DB::table('fee_group_details')
                                ->where('option_code', '=', $option_code)
                                ->where('key_type', '=', $keyType)
                                ->where('fee_type_id', '=', $fee_type)
                                ->where('fee_group_id', '=', $group_id)->update(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => $option_code,
                                        'component_code' => '-'
                                    ]
                                );
                        } else {
                            DB::table('fee_group_details')
                                ->insert(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'fee_group_id' => $group_id,
                                        'fee_type_id' => $fee_type,
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => $option_code,
                                        'component_code' => '-'
                                    ]
                                );
                        }
                    }

                    return response()->json(['success' => 'Fee detail added successfully']);
                    break;
                case 'C':
                    $feegroup_details = (object)$request->feegroup_detail;
                    foreach ($feegroup_details->component_code as $key => $component_code) {
                        $availableChecker = DB::table('fee_group_details')
                            ->select('key_type')
                            ->where('component_code', '=', $component_code)
                            ->where('key_type', '=', $keyType)
                            ->where('fee_type_id', '=', $fee_type)
                            ->where('fee_group_id', '=', $group_id)
                            ->first();
                        if ($availableChecker !== null) {
                            DB::table('fee_group_details')
                                ->where('component_code', '=', $component_code)
                                ->where('key_type', '=', $keyType)
                                ->where('fee_type_id', '=', $fee_type)
                                ->where('fee_group_id', '=', $group_id)->update(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => '-',
                                        'component_code' => $component_code
                                    ]
                                );
                        } else {
                            DB::table('fee_group_details')
                                ->insert(
                                    [
                                        'amount' => $feegroup_details->amount[$key],
                                        'fee_group_id' => $group_id,
                                        'fee_type_id' => $fee_type,
                                        'key_type' => $keyType,
                                        'subject_code' => '-',
                                        'option_code' => '-',
                                        'component_code' => $component_code
                                    ]
                                );
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
    }


    public function updateDetail(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
           'fee_type_id' => 'required|string',
            'amount'  => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            DB::table('fee_group_details')
                ->where('id', '=', $id)  // find your user by their email
                ->update(array(
                    'amount' => $request->amount,
                    'fee_type_id' => $request->fee_type_id,
                ));
            return response()->json(['success' => 'Fee updated the records successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'session_id' => ['required', Rule::unique('fee_groups')->where(function ($query) use ($request) {
                return $query->where('session_id', $request->session_id)
                    ->where('level_id', $request->level_id)
                    ->where('candidate_type', $request->candidate_type);
            })->ignore($id)],
            'level_id' => 'required|string',
            'candidate_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $feegroup = FeeGroup::find($id);
            $feegroup->name = $request->name;
            $feegroup->description = $request->description;
            $feegroup->session_id = $request->session_id;
            $feegroup->level_id = $request->level_id;
            $feegroup->candidate_type = $request->candidate_type;
            $feegroup->save();
        }
        return response()->json(['success' => "Fee group updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FeeGroup::find($id)->delete();
        return response()->json(['success' => 'Fee Group deleted successfully.']);
    }

    public function destroyDetail($id)
    {

        DB::table('fee_group_details')->where('id', $id)->delete();
        return response()->json(['success' => 'Record deleted successfully.']);
    }



    public function feeDetail(Request $request)
    {

        $keyType = $request->key_type;
        $feedetails = FeeGroup::with('feetypes')->find($request->fee_group_id);
        $outputHTML = '';
        switch ($keyType) {
            case 'L':
                $outputHTML = "
                <div class='form-group'>
                <label class='form-label'>Amount</label>
                <input type='text'class='form-control' name='feegroup_detail[amount][]'>
                </div>";
                return response()->json(['output' => $outputHTML]);
                break;
            case 'S':
                $subjects = Subject::whereHas('sessions', function ($q) use ($feedetails) {
                    $q->where('sessions.id', '=', $feedetails->session_id);
                })->where('level', '=', $feedetails->level_id)->get();

                
                $outputHTML = "
                <div class='row'>
                ";

                foreach ($subjects as $key =>  $subject) {
                    $is_practical=$subject->is_practical=='1'?'Practical':'';
                    $is_delf=$subject->is_delf=='1'?'Delf':'';


                    $outputHTML .= "
                    <div class='col-md-4'>
                      <div class='custom-control custom-checkbox'>

                          <input type='checkbox' value='$subject->subject_code' name='feegroup_detail[subject_code][]' class='custom-control-input' id='$subject->subject_code'>
                          <label class='custom-control-label' for='$subject->subject_code'>$subject->subject_name ($subject->subject_code) $is_delf $is_practical</label>
                      </div>
                      <input type='text'class='form-control' name='feegroup_detail[amount][]' placeholder='Amount: e.g 150'>
                    </div>

                  ";
                }
                $outputHTML .= "<div class='clearfix'></div></div>";
                return response()->json(['output' => $outputHTML]);
                break;
            case 'C':
                $components = DB::table('subjects')
                    ->select('subjects.subject_code', 'subjects.subject_name', 'subjects.discipline', 'subjects.is_practical', 'subjects.is_delf', 'option_heads.option_code', 'components.component_code', 'components.component_name', 'sessions.session', 'sessions.financial_year')
                    ->join('session_subject', 'session_subject.subject_code', '=', 'subjects.subject_code')
                    ->join('sessions', 'sessions.id', '=', 'session_subject.session_id')
                    ->join('subject_option', 'subject_option.subject_code', '=', 'subjects.subject_code')
                    ->join('components', 'components.subject_code', '=', 'subjects.subject_code')
                    ->join('option_heads', 'option_heads.option_code', '=', 'subject_option.option_code')
                    ->groupBy('components.component_code')
                    ->where('subjects.level', '=',  $feedetails->level_id)
                    ->where('sessions.id', '=',  $feedetails->session_id)
                    ->get();
                $outputHTML = "<div class='row'>";

                foreach ($components as $key =>  $component) {
                    $outputHTML .= "
                        <div class='col-md-4'>
                          <div class='custom-control custom-checkbox'>
                              <input type='checkbox' value='$component->component_code' name='feegroup_detail[component_code][]' class='custom-control-input' id='$component->component_code'>
                              <label class='custom-control-label' for='$component->component_code'>Paper $component->component_code</label>
                          </div>
                          <input type='text'class='form-control' name='feegroup_detail[amount][]' placeholder='Amount: e.g 150'>
                        </div>

                      ";
                }
                $outputHTML .= "<div class='clearfix'></div></div>";
                return response()->json(['output' => $outputHTML]);
                break;
            case 'O':
                $options = DB::table('subjects')
                    ->select('subjects.subject_code', 'subjects.subject_name', 'subjects.discipline', 'subjects.is_practical', 'subjects.is_delf', 'option_heads.option_code', 'components.component_code', 'components.component_name', 'sessions.session', 'sessions.financial_year')
                    ->join('session_subject', 'session_subject.subject_code', '=', 'subjects.subject_code')
                    ->join('sessions', 'sessions.id', '=', 'session_subject.session_id')
                    ->join('subject_option', 'subject_option.subject_code', '=', 'subjects.subject_code')
                    ->join('components', 'components.subject_code', '=', 'subjects.subject_code')
                    ->join('option_heads', 'option_heads.option_code', '=', 'subject_option.option_code')
                    ->groupBy('option_heads.option_code')
                    ->where('subjects.level', '=',  $feedetails->level_id)
                    ->where('sessions.id', '=',  $feedetails->session_id)
                    ->get();
                $outputHTML = "<div class='row'>";

                foreach ($options  as $key =>  $option) {
                    $outputHTML .= "
                    <div class='col-md-4'>
                      <div class='custom-control custom-checkbox'>
                          <input type='checkbox' value='$option->option_code' name='feegroup_detail[option_code][]' class='custom-control-input' id='$option->option_code'>
                          <label class='custom-control-label' for='$option->option_code'>$option->option_code</label>
                      </div>
                      <input type='text'class='form-control' name='feegroup_detail[amount][]' placeholder='Amount: e.g 150'>
                    </div>
                  ";
                }
                $outputHTML .= "<div class='clearfix'></div></div>";
                return response()->json(['output' => $outputHTML]);
                break;

            default:
                break;
        }
    }
}
