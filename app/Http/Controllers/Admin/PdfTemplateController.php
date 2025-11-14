<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdfTemplate;
use App\Models\PdfTemplateCategory;
use App\Services\PDFService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PdfTemplateController extends Controller
{
    public function index(Request $request)
    {



        if ($request->ajax()) {
            $templates = PdfTemplate::query();
            return DataTables::of($templates)
                ->editColumn('actions', function ($row) {
                    $html = "<a href='" . route('admin.pdf.templates.show', $row->id) . "'  class='btn btn-sm btn-primary'><i class='fas fa-eye'></i> View</a>
                             <button type='button' class='btn btn-sm btn-primary edit-template' data-url='" . route('admin.pdf.templates.edit', $row->id) . "'><i class='fa fa-edit'></i> Edit</button>
                             <button type='button' class='btn btn-sm btn-danger delete-template' data-url='" . route('admin.pdf.templates.destroy', $row->id) . "'><i class='fa fa-trash'></i>  Delete</button>";
                    return     $html;
                })
                ->editColumn('designer', function ($row) {
                    $html = "<a href='" . route('admin.pdf.designer.index', $row->id) . "'  class='btn btn-sm btn-primary'><i class='fas fa-ruler'></i> Design</a>";
                    return     $html;
                })
                ->rawColumns(['actions', 'designer'])
                ->make(true);
        }
        $categories = PdfTemplateCategory::get();

        $availableTables = $this->getAvailableViews();



        // Get columns for the first table if available

        return view('admin.pdf.template', compact('categories', 'availableTables'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:pdf_templates,name,NULL,id",
            'description' => 'required',
            'orientation' => 'required',
            'category_id' => 'required',
            'thumbnail' => 'required_with:is_blank|mimes:pdf|max:1024',
            'is_blank' => 'sometimes',
            'data_source' => 'required_with:columns',
            'columns' => 'sometimes',
            'is_table_filters' => 'required_with:columns',

        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $category = PdfTemplateCategory::find($request->category_id);
        $path = '';
        if ($request->has('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->storeAs("$category->name", time() . '.' . $request->file('thumbnail')->getClientOriginalExtension(),  'public');
            if ($thumbnail == null ||   $thumbnail == '') {
                return response()->json(['thumbnail' => ['Error in storing document in local']]);
            }
            $path = "/storage/$thumbnail";
        }



        $keys = $request->input('columns', []);
        $values = $request->input('column_values', []);

        $columns = [];
        foreach ($keys as $i => $key) {
            if (!empty($key)) {
                $columns[$key] = $values[$i] ?? null;
            }
        }


        $pdfTemplate = new  PdfTemplate();
        $pdfTemplate->name = $request->name;
        $pdfTemplate->description = $request->description;
        $pdfTemplate->category_id = $request->category_id;
        $pdfTemplate->orientation = $request->orientation;
        $pdfTemplate->data_source = $request->data_source;
        $pdfTemplate->column_filters = $columns;
        $pdfTemplate->is_blank =  $request->is_blank || 0;
        $pdfTemplate->is_table_filters =  $request->is_table_filters || 0;
        $pdfTemplate->thumbnail = $path;
        $pdfTemplate->save();
        return response()->json(['success' => "successfully added the record"]);
    }


    public function edit($id)
    {
        $pdfTemplate = PdfTemplate::find($id);
        $url = route('admin.pdf.templates.update', $id);
        return response()->json(['template' => $pdfTemplate, 'url' => $url]);
    }


    public function show($id)
    {
         $pdfTemplate = PdfTemplate::find($id);
         $culumns = $this->getTableColumns($pdfTemplate->data_source);
         $filteredData = collect($pdfTemplate->column_filters)->reject(function ($value) {
            return is_null($value);
        })->all();

          $values = (object)$filteredData;

         $filteredData=  array_map(function ($value) use ( $culumns) {
            if (in_array( $value,$culumns)) {
                return [
                    'column' => $value,
                    'operator' => "equals"
                ];
            }
        }, array_keys($filteredData));

         $felters =array_filter($filteredData, function ($value) {
            return $value !== null;
        });

         $pdf = new  PDFService();
        $pdf->generatePdf($id, $felters, $values,1);
        exit();
    }


    public function destroy($id)
    {

        DB::beginTransaction();
        try {
            // Perform database operations
            $pdfTemplate = PdfTemplate::find($id);
            DB::table('pdf_template_blocks')->where('template_id', $id)->delete();
            $pdfTemplate->delete();
            DB::commit();
            return response()->json(['success' => "successfully deleted the record"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Operation failed: ' . $e->getMessage()]);
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
            'name' => "required|unique:pdf_templates,name,$id,id",
            'description' => 'required',
            'orientation' => 'required',
            'category_id' => 'required',
            'thumbnail' => 'required_with:is_blank|mimes:pdf|max:1024',
            'is_blank' => 'sometimes',
            'data_source' => 'required_with:columns',
            'columns' => 'sometimes',
            'is_table_filters' => 'required_with:columns',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $pdfTemplate =  PdfTemplate::find($id);
        $path = '';
        if ($request->has('thumbnail')) {
            $category = PdfTemplateCategory::find($request->category_id);
            $thumbnail = $request->file('thumbnail')->storeAs("$category->name", time() . '.' . $request->file('thumbnail')->getClientOriginalExtension(),  'public');
            if ($thumbnail == null ||   $thumbnail == '') {
                return response()->json(['thumbnail' => ['Error in storing document in local']]);
            }
            if (Storage::disk('public')->exists($pdfTemplate->thumbnail)) {
                Storage::disk('public')->delete($pdfTemplate->thumbnail);
            }
            $path = "/storage/$thumbnail";
        }

        $keys = $request->input('columns', []);
        $values = $request->input('column_values', []);
        $columns = [];
        foreach ($keys as $i => $key) {
            if (!empty($key)) {
                $columns[$key] = $values[$i] ?? null;
            }
        }

        $pdfTemplate->name = $request->name;
        $pdfTemplate->category_id = $request->category_id;
        $pdfTemplate->description = $request->description;
        $pdfTemplate->orientation = $request->orientation;
        $pdfTemplate->data_source = $request->data_source;
        $pdfTemplate->is_table_filters =  $request->is_table_filters || 0;
        $pdfTemplate->column_filters = $columns;
        $pdfTemplate->is_blank =  $request->is_blank || 0;
        $pdfTemplate->thumbnail = $path;
        $pdfTemplate->save();
        return response()->json(['success' => "successfully updated the record"]);
    }

    protected function getAvailableTables()
    {
        // For MySQL/MariaDB
        if (config('database.default') == 'mysql') {
            $tables = DB::select('SHOW TABLES');


            $key = 'Tables_in_' . config('database.connections.mysql.database');
            return array_map(function ($table) use ($key) {
                return $table->{$key};
            }, $tables);
        }
        // For PostgreSQL
        elseif (config('database.default') == 'pgsql') {
            return DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        }
        // For SQLite
        elseif (config('database.default') == 'sqlite') {
            return DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        }
        // For SQL Server
        elseif (config('database.default') == 'sqlsrv') {
            return DB::select("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE'");
        }

        return [];
    }


    protected function getViews()
    {
        $databaseName = config('database.connections.mysql.database');
        return collect(DB::select("
        SELECT TABLE_NAME as name, VIEW_DEFINITION as definition
        FROM INFORMATION_SCHEMA.VIEWS
        WHERE   TABLE_NAME LIKE 'report%' AND TABLE_SCHEMA = ?
    ", [$databaseName]));
    }


    protected function getAvailableViews()
    {
        $databaseName = config('database.connections.mysql.database');
        return collect(DB::select("
        SELECT TABLE_NAME as name, VIEW_DEFINITION as definition
        FROM INFORMATION_SCHEMA.VIEWS
        WHERE   TABLE_NAME LIKE 'report%' AND TABLE_SCHEMA = ?
    ", [$databaseName]))->mapWithKeys(function ($view, $key) {
            return [$key => $view->name];
        });
    }

    protected function getPrimaryKey($tableName)
    {

        $primaryKey = DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = '$tableName'
                AND COLUMN_KEY = 'PRI'
            ");

        return  isset($primaryKey[0]) ? $primaryKey[0]->COLUMN_NAME : 'id';
    }

    protected function getViewColumns($tableName)
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM $tableName");
            $key = 'Field';
            return array_map(function ($table) use ($key) {
                return $table->{$key};
            }, $columns);
        } catch (\Exception $e) {
            return [];
        }
    }
     protected function getTableColumns($tableName)
    {
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }
}
