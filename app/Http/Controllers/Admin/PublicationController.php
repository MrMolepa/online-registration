<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    public function index()
    {
        return view('admin.publications.publication');
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
                            <th>Pubication at</th>
                            <th>Action</th>
                        </tr>
                    </thead>';

        $results =Publication::get();
        
       

        foreach ($results as $result) {

            $newDate = date('Y-m-d\TH:i:s', strtotime($result->published_at));
            $value .= '<tr id="' . $result->id . '">
                            <td>' . $result->id . '</td>
                            <td>' . $result->title . '</td>
                            <td>' . $result->display_name . '</td>
                         
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

    public function update(Request $request)
    {
        $id = $request->id;
        $published_at = date("Y-m-d\TH:i:s", strtotime($request->dateTime));
       
        try {

            DB::table('publications')
                ->where('id', '=', $id)  // find your user by their email
                ->update(array(
                    'published_at' =>$published_at,
                
                ));
            return response()->json(['status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['status' => $e]);
        }
    }



}
