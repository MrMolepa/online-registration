<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Yajra\DataTables\Facades\DataTables;

class DocumentCategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $categories = DocumentCategory::with('childs')->where('parent_id', '=', null);
            return DataTables::of($categories)

                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.document-categories.edit', $row->id) . "'> Edit</button>
                          <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.document-categories.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->editColumn('subcategories', function ($model) {
                    $subcategories = $model->childs;
                    $subcategories->map(function ($subcategory) {
                        $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.document-categories.edit', $subcategory->id) . "'> Edit</button>
                           <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.document-categories.destroy', $subcategory->id) . "'> Delete</button>";
                        $subcategory['actions'] =     $html;
                        return $subcategory;
                    });
                    return   $subcategories;
                })
                ->rawColumns(['actions', 'subcategories'])
                ->make(true);
        }

        return view('admin.documents.categories.index');
    }


    public function store(Request $request)
    {


        ini_set('memory_limit','2048M');


        $validator = Validator::make($request->all(), [
            'name' => "required|unique:document_categories,name,NULL,id,deleted_at,NULL",
            'description' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DocumentCategory::create($request->all());
        return response()->json(['success' => "successfully added the record"]);
    }


    public function edit($id)
    {
        $category = DocumentCategory::find($id);
        $url=route('admin.document-categories.update',$id);
        return response()->json(['category' =>  $category,'url'=>$url]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:document_categories,name,$id,id,deleted_at,NULL",
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $documentCategory = DocumentCategory::find($id);
        $documentCategory->name=$request->name ;
        $documentCategory->description =$request->description;
        $documentCategory->save();

        return response()->json(['success' => "successfully updated the record"]);
    }

    public function destroy($id)
    {
        $document = Documents::where('category_id', '=', $id)->first();
        if (!is_null($document)) {
            return response()->json(['error' => "successfully updated the record"]);
        } else {

            $category = DocumentCategory::find($id);
            $category->delete();
            return response()->json(['success' => "successfully updated the record"]);
        }
    }

    public function subcategories($id)
    {
        // return response()->json($this->categoryRepository->findWhere(['parentId' => $id]));
    }

    public function GetAllCategoriesForDropDown()
    {
        // return response()->json($this->categoryRepository->all());
    }
}
