<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use App\Models\Publication;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PublicationController extends Controller
{
    public function index()
    {

        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get();
        $sessions = Session::where('financial_year', $years->first()->year)->get();
        $levels =  CenterCandidate::select(DB::raw('level'))
            ->where('financial_year', $years->first()->year)
            ->orderBy('level', 'DESC')
            ->distinct()
            ->get();
        return view('admin.publications.publication', compact('levels', 'sessions'));
    }

    public function displayPublications()
    {
        $value = '';
        $value = '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Display Name</th>
                             <th>Level</th>
                            <th>Session</th>
                            <th>Pubication at</th>
                            <th>Action</th>
                        </tr>
                    </thead>';

        $results = Publication::get();



        foreach ($results as $result) {

            $newDate = date('Y-m-d\TH:i:s', strtotime($result->published_at));
            $value .= '<tr id="' . $result->id . '">
                            <td>' . $result->id . '</td>
                            <td>' . $result->title . '</td>
                            <td>' . $result->display_name . '</td>
                            <td>' . $result->level . '</td>
                            <td>' . $result->session . '</td>

                            <td>
                                    <span class="editSpan dateTime"> ' . date('d-m-Y  H:i', strtotime($result->published_at)) . '</span>
                                    <input class="editInput dateTime" type="datetime-local" name="dateTime" value="' . $newDate . '">

                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary editBtn" >Edit</button>
                                <button type="button" class="btn btn-sm btn-success saveBtn" >Save</button>
                            </td>
                    </tr>';
        }
        $value .= '</tbody>
                 </table>';

        return response()->json(['status' => 1, 'table' => $value]);
    }

    public function update(Request $request, $id)
    {
        $id = $request->id;
        $published_at = date("Y-m-d\TH:i:s", strtotime($request->dateTime));

        try {

            DB::table('publications')
                ->where('id', '=', $id)  // find your user by their email
                ->update(array(
                    'published_at' => $published_at,

                ));
            return response()->json(['status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['status' => $e]);
        }
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'display_name' => 'required',
            'level' => ['required', Rule::unique('publications')->where(function ($query) use ($request) {
                return $query->where('session', $request->input('session'))
                    ->where('level', $request->input('level'));
            })],
            'session' => 'required',
            'published_at' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $published_at = date("Y-m-d\TH:i:s", strtotime($request->published_at));
        try {
            Publication::create(
                [
                    'title' => $request->input('title'),
                    'display_name' => $request->input('display_name'),
                    'level' => $request->input('level'),
                    'session' => $request->input('session'),
                    'published_at' => $published_at,
                    'publish' => $request->input('publish') ?? '0'
                ]
            );
            return response()->json(['success' => 'Successfully added the rocords']);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['title' => ['internal error']]]);
        }
    }
}
