<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings =  Setting::where('meta_field', '=', 'business_address')->first();

        if ($request->ajax()) {
            $settings =  Setting::query();
            return DataTables::eloquent($settings)
                ->setRowId('id')
                ->editColumn('meta_field', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'>$row->meta_field</span>
                    <input class='editInput period form-control' type='text' name='meta_field' value='$row->meta_field'>
                    </div>";
                    return   $html;
                })->editColumn('meta_value', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->meta_value</span>
                    <input class='editInput period form-control' type='text' name='meta_value' value='$row->meta_value'>
                    </div>";
                    return     $html;
                })->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.setting.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.fees.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns([
                    'meta_field',
                    'meta_value',
                    'action'
                ])
                ->toJson();
        }
        return view('admin.settings.system');
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'meta_field' => 'required|max:255',
            'meta_value' => 'required|max:255',
        ]);
        DB::table('settings')->where('id', '=', $id)->update([
            'meta_field' => $request->meta_field,
            'meta_value' => $request->meta_value,
        ]);
        return response()->json(['success' => "Successfully"]);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'meta_field' => 'required|max:255',
            'meta_value' => 'required|max:255',
        ]);
        DB::table('settings')->insert([
            'meta_field' => $request->meta_field,
            'meta_value' => $request->meta_value,
        ]);
        return response()->json(['success' => "Successfully"]);
    }
    public function distroy($id)
    {

        $setting = Setting::findOrFail($id);
        $setting->delete();
        return response()->json(['success' => "Successfully"]);
    }
}
