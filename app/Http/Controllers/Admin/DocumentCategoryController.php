<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DocumentCategoryController extends Controller
{
    public function index(Request $request){
         
        if ($request->ajax()) {
            $categories=DocumentCategory::with('childs')->where('parent_id','=',null);
            return DataTables::of($categories)

            ->editColumn('actions', function ($row) {
                $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.document-categories.edit',$row->id) . "'> Edit</button>
                          <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.document-categories.destroy', $row->id) . "'> Delete</button>";
                return     $html;
            })
            ->editColumn('subcategories', function ($model) {
                $subcategories =$model->childs;
                $subcategories->map(function ($subcategory)
                {
                   $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.document-categories.edit',$subcategory->id) . "'> Edit</button>
                           <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.document-categories.destroy', $subcategory->id) . "'> Delete</button>";
                   $subcategory['actions'] =     $html ;
                    return $subcategory;
                });
                return   $subcategories;
            })
            ->rawColumns(['actions','subcategories'])
            ->make(true);
        }




        return view('admin.documents.categories.index');
    }


    public function store(Request $request){

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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:categories,name,$id,id,deleted_at,NULL",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $documentCategory =DocumentCategory::find($id);
        $documentCategory->update($request->all());
        return response()->json(['success' => "successfully updated the record"]);


    }

    public function destroy($id)
    {
        // $isDeleted = $this->categoryRepository->deleteCategory($id);
        // if ($isDeleted == true) {
        //     return response()->json([], 200);
        // } else {
        //     return response()->json([
        //         'message' => 'Category can not be deleted. Document is assign to this category.',
        //     ], 404);
        // }
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
