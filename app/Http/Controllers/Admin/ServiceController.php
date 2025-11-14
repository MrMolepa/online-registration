<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use App\Models\OneTimeService;
use App\Models\OneTimeServicesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        if ($request->ajax()) {

            if ($request->has('service_items')) {
                $oneTimeServiceItems = OneTimeServicesItem::where(['financial_year' => $request->year]);
                return DataTables::eloquent($oneTimeServiceItems)
                    ->setRowId('id')
                    ->editColumn('id', function ($row) {
                        return  $row->id;
                    })
                    ->editColumn('name', function ($row) {
                        $html = "
                        <div class='form-group'>
                        <span class='editSpan period'> $row->name</span>
                        <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                        </div>";
                        return     $html;
                    })
                    ->editColumn('description', function ($row) {
                        $html = "<div class='form-group'>
                        <span class='editSpan period'> $row->description</span>
                        <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                        </div>";
                        return     $html;
                    })
                    ->editColumn('financial_year', function ($row) {
                        $html = "<div class='form-group'>
                        <span class='editSpan period'> $row->financial_year</span>
                        <input class='editInput period form-control' type='text' name='financial_year' value='$row->financial_year'>
                        </div>";
                        return     $html;
                    })
                    ->editColumn('price', function ($row) {
                        $html = "<div class='form-group'>
                        <span class='editSpan period'> $row->price</span>
                        <input class='editInput period form-control' type='text' name='price' value='$row->price'>
                        </div>";
                        return     $html;
                    })
                    ->editColumn('action', function ($row) {
                        $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                                  <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.service-item.update', $row->id) . "'> Save</button>
                                  <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.service-item.destroy', $row->id) . "'> Delete</button>";
                        return     $html;
                    })
                    ->rawColumns(['name', 'description', 'financial_year', 'price', 'action'])
                    ->make(true);
            }


            $services  =  OneTimeService::query();
            return DataTables::eloquent($services)
                ->setRowId('id')
                ->editColumn('id', function ($row) {
                    return  $row->id;
                })
                ->editColumn('name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->name</span>
                    <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                    </div>";
                    return     $html;
                })
                ->editColumn('desciption', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->desciption</span>
                    <input class='editInput period form-control' type='text' name='desciption' value='$row->desciption'>
                    </div>";
                    return     $html;
                })
                ->editColumn('service_item', function ($row) {
                    $html = "<a href='" . route('admin.service-item.index', ['service' => $row->id]) . "' type='button' class='btn  btn-primary' >Service Item</a>";
                    return    $html;
                })
                ->editColumn('emails', function ($row) {
                    $html = "<a href='" . route('admin.service-emails.index', ['service' => $row->id]) . "' type='button' class='btn service-email-btn  btn-primary' >Emails</a>";
                    return    $html;
                })
                ->editColumn('service_item', function ($row) {
                    $html = "<a href='" . route('admin.service-item.index', ['service' => $row->id]) . "' type='button' class='btn  btn-primary' >Service Item</a>";
                    return    $html;
                })
                ->editColumn('requirements', function ($row) {
                    $html = "<a href='" . route('admin.service-requirements.index', ['service' => $row->id]) . "' type='button' class='btn  btn-secondary' >Requirements</a>";
                    return    $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.services.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.services.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['name', 'desciption', 'emails', 'service_item', 'requirements', 'action'])
                ->toJson();
        }
        return view('admin.services.services', compact('years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $prev_year = (date('Y') - 1) . '-' . date('Y');
        $next_year = date('Y') . '-' . (date('Y') + 1);

        $oneTimeServiceItems = OneTimeServicesItem::where(['financial_year' =>   $prev_year])->get();


        foreach ($oneTimeServiceItems as  $oneTimeServiceItem) {
            $original = OneTimeServicesItem::find($oneTimeServiceItem->id);
            $dump = OneTimeServicesItem::where([
                'financial_year' => $next_year,
                'name' => $oneTimeServiceItem->name
            ])->first();

            if ($original &&   !$dump) {
                // Clone the record
                $clone = $original->replicate();

                // Modify specific attributes
                $clone->financial_year = $next_year;
                $clone->created_at = now();
                $clone->updated_at = now();
                $clone->save();
            }

        }

        return response()->json(['success' =>  'sucess']);
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
            'name' => 'required',
            'desciption' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $oneTimeService = new OneTimeService();
        $oneTimeService->name = $request->name;
        $oneTimeService->desciption = $request->desciption;
        $oneTimeService->save();
        return response()->json(['success' =>  $oneTimeService]);
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
    public function edit($id) {}

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
            'name' => 'required',
            'desciption' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $oneTimeService = OneTimeService::findOrFail($id);
        $oneTimeService->name = $request->name;
        $oneTimeService->desciption = $request->desciption;
        $oneTimeService->save();
        return response()->json(['success' =>  $oneTimeService]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $oneTimeService = OneTimeService::findOrFail($id);
        $oneTimeService->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
