<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Libraries\fpdfcertificate\easyTable;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\Center;


use App\Models\PdfTemplate;
use App\Models\PdfTemplateBlock;
use Illuminate\Support\Facades\DB;


class PdfController extends Controller
{






    public function index($id)
    {


        $elements = PdfTemplateBlock::where('template_id', '=', $id)->get();
        $availableTables = $this->getAvailableTables();
        $template = PdfTemplate::find($id);

        // Get columns for the first table if available
        $availableColumns = [];
        if (!empty($availableTables)) {
            $availableColumns = $this->getTableColumns($availableTables[0]);
        }
        return view('admin.pdf.pdf', compact('elements', 'availableTables', 'availableColumns', 'template'));
    }






    protected function addElementToPdf($element)
    {
        // ... existing code ...

        if ($element->data_source_table) {
            $columns = explode(',', $element->data_source_columns);
            $data = DB::table($element->data_source_table)
                ->select($columns)
                ->get()
                ->toArray();

            $element->content = json_encode($data);
        }

        // ... rest of the code ...
    }


    public function saveElementPositions(Request $request)
    {
        $elements = $request->input('elements');

        foreach ($elements as $elementData) {
            $element = PdfTemplateBlock::find($elementData['id']);
            if ($element) {
                $element->x_position = $elementData['x'];
                $element->y_position = $elementData['y'];
                $element->width = $elementData['width'];
                $element->height = $elementData['height'];
                $element->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function findElement( $id){
        $element = PdfTemplateBlock::findOrFail($id);
        return response()->json(['element' =>  $element]);

    }

    public function storeElement(Request $request)
    {
        $validated = $request->validate([
            'element_type' => 'required|in:text,table,image,barcode',
            'template_id' => 'nullable',
            'x_position' => 'required|numeric|min:0',
            'y_position' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:10',
            'height' => 'required|numeric|min:10',
            'content' => 'nullable|string',
            'font_family' => 'nullable|string|max:50',
            'font_size' => 'nullable|numeric|min:1|max:72',
            'font_style' => 'nullable|string|in:B,I,U,BI,BU,IU,BIU',
            'color' => 'nullable|string|regex:/^#[a-f0-9]{6}$/i',
            'alignment' => 'nullable|string|in:L,C,R',
            'data_columns' => 'nullable',
            'is_dynamic' => 'nullable',
            'filters' => 'nullable',

        ]);

        // Set default content based on element type
        if ($validated['element_type'] === 'table' && empty($validated['content'])) {
            $validated['content'] = '[]'; // Empty JSON array for tables
        } elseif ($validated['element_type'] === 'text' && empty($validated['content'])) {
            $validated['content'] = 'New Text Element';
        }


        // Set default font if not specified
        if ($validated['element_type'] === 'text') {
            $validated['font_family'] = $validated['font_family'] ?? 'Arial';
            $validated['font_size'] = $validated['font_size'] ?? 12;
        }


        $element = PdfTemplateBlock::create($validated);

        return response()->json([
            'success' => true,
            'element' => $element,
            'message' => 'Element created successfully'
        ]);
    }

    public function updateElement(Request $request, $id)
    {
        $element = PdfTemplateBlock::findOrFail($id);


        $validated = $request->validate([
            'x_position' => 'nullable|numeric',
            'y_position' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'rotation' => 'nullable',
            'is_rotated' => 'nullable',
            'font_family' => 'nullable',
            'font_size' => 'nullable',
            'content' => 'nullable'
        ]);






        $element->update($validated);


        return response()->json(['success' => true]);
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



    protected function getTableColumns($tableName)
    {
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getViewColumns($tableName)
    {
        try {


            $columns = DB::select('SHOW TABLES');
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }


    public function destroyElement($id)
    {
        $element =  PdfTemplateBlock::findOrFail($id);
        $element->delete();
        return response()->json(['success' => true]);
    }
}
