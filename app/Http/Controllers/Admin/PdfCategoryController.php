<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdfTemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PdfCategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $categories = PdfTemplateCategory::with('children')->where('parent_id', '=', null);
            return DataTables::of($categories)

                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.pdf.categories.edit', $row->id) . "'> Edit</button>
                          <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.pdf.categories.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->editColumn('subcategories', function ($model) {
                    $subcategories = $model->children;
                    $subcategories->map(function ($subcategory) {
                        $html = "<button type='button' class='btn btn-sm btn-success edit-category' data-url='" . route('admin.pdf.categories.edit', $subcategory->id) . "'> Edit</button>
                           <button type='button' class='btn btn-sm btn-danger delete-category' data-url='" . route('admin.pdf.categories.destroy', $subcategory->id) . "'> Delete</button>";
                        $subcategory['actions'] =     $html;
                        return $subcategory;
                    });
                    return   $subcategories;
                })
                ->rawColumns(['actions', 'subcategories'])
                ->make(true);
        }
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => "required|unique:pdf_template_categories,name,NULL,id",
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        PdfTemplateCategory::create($request->all());
        return response()->json(['success' => "successfully addad the record"]);
    }


    public function edit($id)
    {
        $category = PdfTemplateCategory::find($id);
        $url = route('admin.pdf.categories.update', $id);
        return response()->json(['category' =>  $category, 'url' => $url]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:pdf_template_categories,name,$id,id",
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $category = PdfTemplateCategory::find($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return response()->json(['success' => "successfully updated the record"]);
    }

    public function destroy($id)
    {
        // $document = Documents::where('category_id', '=', $id)->first();
        // if (!is_null($document)) {
        //     return response()->json(['error' => "successfully updated the record"]);
        // } else {

        //     $category = DocumentCategory::find($id);
        //     $category->delete();
        //     return response()->json(['success' => "successfully updated the record"]);
        // }
    }
}
