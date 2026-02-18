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
use App\Models\InvigilationCatergories;
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

    if ($request->ajax()) {
        // Check if this is for the "All Centers" tab
        if ($request->has('all_centers') && $request->all_centers == true) {
            return $this->allCenters($request);
        }
        
        // Original "Center Accounts" tab code
        try {
            // Build the query - load users with their role
            $query = Center::with(['users' => function($q) {
                $q->where('user_type', 'center')->with('role');
            }]);
            
            // Apply level filter if provided
            if ($request->has('level') && $request->level) {
                $level = Level::find($request->level);
                if ($level) {
                    $query->where('level', $level->level);
                }
            }
            
            // Only get centers that have center users
            $query->whereHas('users', function ($q) {
                $q->where('user_type', 'center');
            });
            
            return DataTables::eloquent($query)
                ->addColumn('center_no', function ($row) {
                    return $row->center_no ?? '';
                })
                ->addColumn('center_name', function ($row) {
                    return $row->center_name ?? '';
                })
                ->addColumn('email', function ($row) {
                    $email = '';
                    if ($row->users && $row->users->isNotEmpty()) {
                        $email = $row->users->first()->email ?? '';
                    }
                    return "<span class='editSpan period'>{$email}</span>
                            <input class='editInput period form-control' type='text' name='email' value='{$email}' style='display:none;'>";
                })
                ->addColumn('centre_account_password', function ($row) {
                    if ($row->users && $row->users->isNotEmpty()) {
                        return $row->users->first()->centre_account_password ?? '';
                    }
                    return '';
                })
                ->addColumn('role', function ($row) {
                    if (!$row->users || $row->users->isEmpty()) {
                        return 'No user assigned';
                    }
                    
                    $user = $row->users->first();
                    $userRole = $user->role;
                    
                    $roles = Role::whereIn('name', ['center-admin', 'ldtc-centers', 'center-editor'])->get();
                    $actionUrl = route('admin.centers.changerole', $row->center_no);
                    
                    $html = "<select class='edit-role form-control' data-url='{$actionUrl}' name='role'>";
                    
                    $currentRoleId = $userRole ? $userRole->id : null;
                    
                    foreach ($roles as $role) {
                        $selected = ($role->id == $currentRoleId) ? 'selected' : '';
                        $html .= "<option value='{$role->id}' {$selected}>{$role->display_name}</option>";
                    }
                    
                    $html .= "</select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    if (!$row->users || $row->users->isEmpty()) {
                        return '<span class="text-danger">No user</span>';
                    }
                    
                    $user = $row->users->first();
                    $status = $user->status ?? 0;
                    $icon = $status == 0 
                        ? "<i class='fa fa-unlock' aria-hidden='true'></i>" 
                        : "<i class='fa fa-lock' aria-hidden='true'></i>";
                    $iconReset = "<i class='fa fa-key' aria-hidden='true'></i>";
                    
                    $editUrl = route('admin.centers.edit', $row->center_no);
                    $resetUrl = route('admin.centers.resetCenterPassword', $user->id);
                    
                    return "
                        <button type='button' title='edit' class='btn btn-sm btn-primary editBtn-account' data-url='{$editUrl}'>Edit</button>
                        <button data-url='' class='btn btn-sm btn-info editStatusBtn'>{$icon}</button>
                        <button data-url='{$resetUrl}' class='btn btn-sm btn-info resetBtn'>{$iconReset}</button>
                    ";
                })
                ->rawColumns(['email', 'role', 'action'])
                ->make(true);
                
        } catch (\Exception $e) {
            \Log::error('DataTables Center Index Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    // Non-AJAX request - return view
    $levels = Level::all();
    $catergories = InvigilationCatergories::all();
    $sessions = Session::groupBy('session')->pluck('session')->toArray();
    $districts = Center::select('district', 'district_code')
        ->whereNotNull('district_code')
        ->groupBy('district_code', 'district')
        ->get();
        
    return view('admin.centers.centers', compact('levels', 'catergories', 'sessions', 'districts'));
}
    public function allCenters(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        if ($request->ajax()) {
            $centers = Center::query();
            if ($request->get('level')) {
                $level = Level::where('id', '=', $request->level)->first();
                $centers = $centers->where('level', $level->level);
            }
            return DataTables::eloquent($centers)
                ->editColumn('center_no', function ($row) {
                    return $row->center_no;
                })
                ->editColumn('center_full_name', function ($row) {
                    return $row->center_full_name;
                })
                ->editColumn('center_name', function ($row) {
                    return $row->center_name;
                })
                ->editColumn('levels', function ($row) {
                    $html = "
                      <a href='" . route('admin.levels.index', ['center_no' => $row->center_no]) . "' class='btn btn-sm btn-primary'>
                        Levels
                       </a>";
                    return $html;
                })
                ->editColumn('subjects', function ($row) {
                    $html = "
                      <a href='" . route('admin.subjects.edit', $row->center_no) . "' class='btn btn-sm btn-primary'>
                        Subjects
                       </a>";
                    return $html;
                })
                ->editColumn('sessions', function ($row) {
                    $html = "
                      <a href='" . route('admin.sessions.index', ['center_no' => $row->center_no]) . "' class='btn btn-primary'>
                         Sessions
                       </a>";
                    return $html;
                })
                ->editColumn('action', function ($row) {

                    $status = $row->status;
                    $icon = $status == 0 ? "<i class='fa fa-eye' aria-hidden='true'></i>" : "<i class='fa fa-eye-slash' aria-hidden='true'></i>";
                    $html = "
                    <a href='" . route('admin.centers.updateStatus', ['center_no' => $row->center_no, 'status' => $status]) . "' class='btn btn-sm btn-info check-status'>  $icon </a>
                    <button type='button' title='edit' class='btn btn-sm btn-danger deleteBtn'  data-url='" . route('admin.centers.destroy', $row->center_no) . "' > Delete</button>
                    ";
                    return $html;
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
            'district_code' => 'required|max:255',
            'address' => 'required|max:255',
            'center_full_name' => 'required|max:255',
            'level' => 'required|max:255',
            'category_id' => 'required|max:255',
            'sessions' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $district = Center::groupBy('district_code')
            ->where('district_code', $request->district_code)
            ->first();
        $password = bin2hex(random_bytes(3));
        $sessions = json_encode($request->sessions);
        Center::create([
            'center_no' => $request->center_no,
            'center_name' => $request->center_name,
            'district' => $district->district,
            'district_code' => $district->district_code,
            'address' => $request->address,
            'level' => $request->level,
            'center_full_name' => $request->center_full_name . " " . $district->district,
            'district_address' => $request->address . " " . $district->district,
            'category_id' => $request->category_id,
            'sessions' => $sessions
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
        $center->status = $status;
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
    $user = User::where('center_no', '=', $id)
                ->where('user_type', '=', 'center')
                ->first();
    
    if ($user) {
        // Update the role_id directly instead of using syncRoles
        $user->role_id = $request->role;
        $user->save();
        
        return response()->json(['success' => 'Successfully updated the role']);
    }
    
    return response()->json(['error' => 'User not found'], 404);
}


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'center_name' => 'required|max:255',
            'district_code' => 'required|max:255',
            'address' => 'required|max:255',
            'level' => 'required|max:255',
            'email' => 'required|max:255|email',
            'category_id' => 'required|max:255',
            'center_full_name' => 'required|max:255',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $district = Center::groupBy('district_code')
            ->where('district_code', $request->district_code)
            ->first();
        $center = Center::where('center_no', '=', $id)->first();
        $center->center_name = $request->center_name;
        $center->district = $district->district;
        $center->district_code = $district->district_code;
        $center->address = $request->address;
        $center->level = $request->level;
        $center->center_full_name = $request->center_full_name . " " . $district->district;
        $center->district_address = $request->address . " " . $district->district;
        $center->category_id = $request->category_id;
        $center->save();
        $user = User::where('center_no', '=', $id)->where('user_type', '=', 'center')->first();
        $user->occupation = $district->district;
        $user->username = $id;
        $user->center_no = $id;
        $user->center_name = $request->center_name;
        $user->email = $request->email;
        $user->save();
        return response()->json(['success' => 'Successfully added the records']);
    }

    public function updateSubjects(Request $request, $id)
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
            return response()->json(['error' => 'Center can not be deleted because has candidates']);
            ;
        } else {
            $center->delete();
            return response()->json(['success' => 'You have successfully deleted  the records']);
        }
    }
}
