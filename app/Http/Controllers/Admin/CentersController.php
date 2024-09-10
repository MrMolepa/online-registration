<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Models\CenterCandidate;
use App\Models\Level;
use App\Models\Role;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CentersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(-1);



        // $center = Center::with('subjects')->where('center_no', '=', "LS505")->first();
        //  $arrayS= json_decode($center->sessions, TRUE);
        // dd($arrayS);






        $allSessionSubjects = DB::table('sessions')
            ->select(
                'subjects.subject_code',
                'subject_option.option_code',
                'subjects.subject_name',
                'subjects.short_name',
                'subjects.level',
                'sessions.id',
                'sessions.session'
            )
            ->join('session_subject', 'sessions.id', '=', 'session_subject.session_id')
            ->join('subjects', 'subjects.subject_code', '=', 'session_subject.subject_code')
            ->join('subject_option', 'subject_option.subject_code', '=', 'subjects.subject_code')
            ->where('sessions.id', '=', 4)
            ->get();
        $allSubjectArray = $allSessionSubjects->pluck('subject_code')->toArray();
        $doubleOptionsSubjes = array_values(array_unique(array_diff_assoc($allSubjectArray, array_unique($allSubjectArray))));
        // dd($doubleOptionsSubjes);

        // dd( $centers->subjects->pluck('subject_code')->toArray(),  );











        //   ->whereHas('subjects', function ($query) use (  $level) {
        //     $query->where('level', '=',   );
        // })->first();


        // Center Accounts
        // $centers = Center::where('center_no', 'LIKE', '1%')->get();
        // foreach ($centers as  $center) {
        //         $password = bin2hex(random_bytes(3));
        //         $user =  User::firstOrNew(array('center_no' => $center->center_no));
        //         $user->user_type = 'center';
        //         $user->occupation = $center->district;
        //         $user->username = $center->center_no;
        //         $user->center_no = $center->center_no;
        //         $user->centre_account_password = $password;
        //         $user->center_name = $center->center_name;
        //         $user->email = $center->center_no."@ecol.org.ls";
        //         $user->password = Hash::make($password);
        //         $user->save();
        //         $user->syncRoles([3]);
        // }





        // Sync Level Center
        //  $centers = Center::where('center_no', 'LIKE', '1%')->get();
        // foreach ($centers as  $center) {
        //     $center->levels()->sync([4]);
        // }









        //  Sync Subject Center G7
        //   $centers = Center::where('center_no', 'LIKE', '1%')->get();
        //     foreach ($centers as  $center) {
        //         $level_id = Level::where('level','=',$center->level)->first()->id;
        //         $subjects = Subject::with('selectedLevel')->whereHas('selectedLevel', function ($query) use ($level_id) {
        //             $query->where('levels.id', '=', $level_id);
        //         })->pluck('subject_code')->toArray();
        //          $center->subjects()->sync($subjects);
        //     }


        // Sync Subject Center
        // foreach ($centers as  $center) {
        //     // $center_subjects = DB::table('center_subjects')
        //     // ->where('center_no', '=', $center->center_no)
        //     // ->where('financial_year', '=', '2023-2024')
        //     // ->where('session', '=', 'November')
        //     // ->pluck('subject_code')->toArray();
        //     // $center->subjects()->sync($center_subjects);
        //     // $center->levels()->sync([1]);
        // }

        // $center_subjects = DB::table('center_subjects')
        //     ->where('center_no', 'like', 'LS%')
        //     ->where('financial_year', '=', '2023-2024')
        //     ->take(10)
        //     ->get();


        // dd($centers->first());
        // dd(   $center_subjects);













        if ($request->ajax()) {
            $centers = Center::with('users', 'users.roles');
            if ($request->level) {
                $level = Level::where('id', '=', $request->level)->first();
                $centers  = $centers->where('level', '=', $level->level);
            }
            $centers->whereHas('users', function ($query) {
                $query->where('user_type', '=', 'center');
            })->get();
            return DataTables::of($centers)
                ->editColumn('center_no', function ($row) {
                    return  $row->center_no;
                })
                ->editColumn('center_name', function ($row) {
                    return  $row->center_name;
                })
                ->editColumn('role', function ($row) {
                    $roles = Role::whereIn('name', ['center-admin', 'ldtc-centers', 'center-editor'])->get();
                    // $rolename = $row->users[0]->roles[0]->display_name;
                    $actionUrl = route('admin.centers.changerole', $row->center_no);
                    $html = "
                    <select class='edit-role form-control' data-url='$actionUrl' name='role'>";
                    foreach ($roles as $role) {
                        $selected = $role->id == $row->users[0]->roles[0]->id ? "selected" : " ";
                        $html .= "<option value='$role->id'  $selected>$role->display_name</option>";
                    }
                    $html .= "</select>";
                    return     $html;
                })
                ->editColumn('email', function ($row) {
                    $email = $row->users[0]->email;
                    $html = "<span class='editSpan period'>$email </span>
                    <input class='editInput period form-control' type='text' name='email' value='$email '>";
                    return     $html;
                })->editColumn('centre_account_password', function ($row) {
                    return    $row->users[0]->centre_account_password;
                })
                ->editColumn('action', function ($row) {
                    $status = $row->users[0]->status;
                    $icon = $status == 0 ? "<i class='fa fa-unlock' aria-hidden='true'></i>" : "<i class='fa fa-lock' aria-hidden='true'></i>";
                    $iconReset = "<i class='fa fa-key' aria-hidden='true'></i>";
                    $html = "<button type='button' title='edit' class='btn btn-sm btn-primary editBtn-account'  data-url='" . route('admin.centers.edit', $row->center_no) . "' > Edit</button>
                             <button data-url='' class='btn btn-sm btn-info editStatusBtn'>  $icon </button>
                             <button  data-url='" . route('admin.centers.resetCenterPassword', $row->users[0]->id) . "' class='btn btn-sm btn-info resetBtn'>   $iconReset</button>
                             ";
                    return     $html;
                })
                ->rawColumns(['center_name', 'email', 'center_name',  'centre_account_password', 'role', 'action'])
                ->make();
        }
        $levels = Level::get();
        return view('admin.centers.centers', compact('levels'));
    }

    public function allCenters(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        if ($request->ajax()) {
            $centers = Center::query();
            if ($request->get('level')) {
                $level = Level::where('id', '=', $request->level)->first();
                $centers =  $centers->where('level', $level->level);
            }
            return DataTables::eloquent($centers)
                ->editColumn('center_no', function ($row) {
                    return  $row->center_no;
                })
                ->editColumn('center_full_name', function ($row) {
                    return    $row->center_full_name;
                })
                ->editColumn('center_name', function ($row) {
                    return  $row->center_name;
                })
                ->editColumn('levels', function ($row) {
                    $html = "
                      <a href='" . route('admin.levels.index', ['center_no' => $row->center_no]) . "' class='btn btn-sm btn-primary'>
                        Levels
                       </a>";
                    return     $html;
                })
                ->editColumn('subjects', function ($row) {
                    $html = "
                      <a href='" . route('admin.subjects.edit', $row->center_no) . "' class='btn btn-sm btn-primary'>
                        Subjects
                       </a>";
                    return     $html;
                })
                ->editColumn('sessions', function ($row) {
                    $html = "
                      <a href='" . route('admin.sessions.index', ['center_no' => $row->center_no]) . "' class='btn btn-primary'>
                         Sessions
                       </a>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {

                    $status = $row->status;
                    $icon = $status == 0 ? "<i class='fa fa-eye' aria-hidden='true'></i>" : "<i class='fa fa-eye-slash' aria-hidden='true'></i>";
                    $html = "
                    <a href='" . route('admin.centers.updateStatus', ['center_no' => $row->center_no, 'status' => $status]) . "' class='btn btn-sm btn-info check-status'>  $icon </a>
                    <button type='button' title='edit' class='btn btn-sm btn-danger deleteBtn'  data-url='" . route('admin.centers.destroy', $row->center_no) . "' > Delete</button>
                    ";
                    return     $html;
                })
                ->rawColumns(['center_name', 'subjects', 'levels', 'sessions', 'center_full_name', 'center_name', 'district', 'action'])
                ->toJson();
        }
    }


    public function resetCenterPassword($id)
    {
        $password = bin2hex(random_bytes(3));
        $user = User::find($id);
        $user->centre_account_password = $password;
        $user->password = Hash::make($password);
        $user->save();
        return response()->json(['success' => 'sucessfully reset the password']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.centers.create');
    }


    public function exportPassword()
    {
        ob_start();
        $results = User::where('user_type', '=', 'center')
            ->where('status', '=', 1)->get();
        $pdf = new exFPDF();
        $pdf->SetMargins(8, 38, 8);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $table = new easyTable($pdf, '%{80, 20}', 'width:180; border:0; font-size:12; line-height:1.2; paddingX:0');
        $table->printRow();
        $count = 0;
        foreach ($results as $result) {
            $count++;
            $table->rowStyle('font-style:B; border:B;border-color:#a1a1a1;');
            $table->rowStyle('min-height:10;paddingY:0.02;');
            $table->easyCell('', 'colspan:4; bgcolor:#000;');
            $table->printRow();
            $table->rowStyle('border:B;border-color:#a1a1a1;');
            $table->easyCell('Center No :' . $result->center_no, 'colspan:2; paddingX:5;');
            $table->printRow();
            $table->easyCell('Center Name :' . $result->center_name, 'colspan:2; paddingX:5;');
            $table->printRow();
            $table->easyCell('Password  :' . $result->centre_account_password, 'colspan:4; paddingX:5; border:B; border-color:#a1a1a1;');
            $table->printRow();

            if ($count == 6) {

                $pdf->AddPage();

                $count = 0;
            }
        }
        $table->endTable(10);
        $pdf->Output('D', "Centres_passwords" . time() . ".pdf");
        ob_end_flush();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'center_no' => 'required|max:255|unique:centers,center_no',
            'center_name' => 'required|max:255',
            'district' => 'required|max:255',
            'district_code' => 'required|max:255',
            'address' => 'required|max:255',
            'level' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $password = bin2hex(random_bytes(3));
        Center::create([
            'center_no' => $request->center_no,
            'center_name' => $request->center_name,
            'district' => $request->district,
            'district_code' => $request->district_code,
            'address' => $request->address,
            'level' => $request->level,
            'center_full_name' => $request->centre_name . " " . $request->district,
            'district_address' => $request->address . " " . $request->district

        ]);
        $user = new User();
        $user->user_type = 'center';
        $user->occupation = $request->district;
        $user->username = $request->center_no;
        $user->center_no = $request->center_no;
        $user->centre_account_password = $password;
        $user->center_name = $request->center_name;
        $user->email = $request->email;
        $user->password = Hash::make($password);
        $user->save();
        $user->syncRoles([3]);
        return response()->json(['success' => 'Successfully added the records']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $center = Center::with('users', 'users.roles')
            ->whereHas('users', function ($query) {
                $query->where('user_type', '=', 'center');
            })->where('center_no', '=', $id)->first();
        $url = route('admin.centers.update', $id);

        return response()->json(['center' => $center, 'url' => $url]);
    }




    public function updateSessions(Request $request, $id)
    {
        $this->validate($request, [
            'sessions' => 'required',
        ]);
        $center = Center::find($id);
        $sessions = json_encode($request->sessions);
        $center->sessions = $sessions;
        $center->save();
        return redirect(route('admin.centers.index'))->with('success', 'You have Successfully update');
    }


    public function updateStatus(Request $request)
    {
        $center_no = $request->center_no;
        $status = $request->status == 0 ? 1 : 0;
        $center = Center::find($center_no);
        $center->status =  $status;
        $center->save();
        return redirect(route('admin.centers.index'))->with('success', 'You have Successfully update');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function changeRole(Request $request, $id)
    {
        $user = User::where('center_no', '=', $id)->where('user_type', '=', 'center')->first();
        $user->syncRoles([$request->role]);
        return response()->json(['success' => 'Successfully added the records']);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'center_name' => 'required|max:255',
            'district' => 'required|max:255',
            'district_code' => 'required|max:255',
            'address' => 'required|max:255',
            'level' => 'required|max:255',
            'email' => 'required|max:255|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $center = Center::where('center_no', '=', $id)->first();
        $center->center_name = $request->center_name;
        $center->district = $request->district;
        $center->district_code = $request->district_code;
        $center->address = $request->address;
        $center->level = $request->level;
        $center->center_full_name = $request->center_name . " " . $request->district;
        $center->district_address = $request->address . " " . $request->district;
        $center->save();
        $user = User::where('center_no', '=', $id)->where('user_type', '=', 'center')->first();
        $user->occupation = $request->district;
        $user->username = $id;
        $user->center_no = $id;
        $user->center_name = $request->center_name;
        $user->email = $request->email;
        $user->save();
        return response()->json(['success' => 'Successfully added the records']);
    }

    public function  updateSubjects(Request $request, $id)
    {
        $this->validate($request, [
            'subjects' => 'required',
        ]);
        $center = Center::find($id);
        $center->subjects()->sync($request->subjects);
        return redirect(route('admin.centers.index'))->with('success', 'You ');
    }



    public function Updatelevels(Request $request, $id)
    {
        $this->validate($request, [
            'levels' => 'required',
        ]);
        $center = Center::find($id);
        $center->levels()->sync($request->levels);
        return redirect(route('admin.centers.index'))->with('success', 'You have ccc');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $center = Center::findOrFail($id);
        $candidates = CenterCandidate::where([
            'center_no' => $id
        ])->count();
        if ($candidates > 0) {
            return response()->json(['error' => 'Center can not be deleted because has candidates']);;
        } else {
            $center->delete();
            return response()->json(['success' =>  'You have successfully deleted  the records']);
        }
    }
}
