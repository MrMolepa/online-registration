<?php

namespace App\Services;

use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\PdfTemplate;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;
use Imagick;


class PDFService
{
    protected $pdf;

    public function __construct()
    {
        // Initialize FPDF
        $this->pdf = new exFPDF();
        $this->pdf->SetCompression(true);
    }

    public  function generatePdf($templateId, $filters = null, $values = null,  $limit = 10)
    {


        // Increase memory temporarily
        ob_start();
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $fileName = 'Entries_' . date('Y-m-d H:i:s') . '.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="document.pdf"'
        ];



        $template =   PdfTemplate::find($templateId);
        $fields = DB::table('pdf_template_blocks')
            ->select(
                'template_id',
                'element_type',
                'data_columns',
                'x_position',
                'y_position',
                'width',
                'height',
                'content',
                'font_family',
                'font_size',
                'font_style',
                'color',
                'alignment',
                'is_dynamic',
                'rotation',
                'is_rotated',
                'filters'
            )->where('template_id', $templateId)->get();
        $processed = 0;
        $primaryKey = $this->getPrimaryKey($template->data_source);
        // Get data
        $query = DB::table($template->data_source)
            ->select("*");
        if ($filters) {
            $this->applyFilters($query, $filters, $values);
        }




        if ($template->is_table_filters == 1 && $template->column_filters !== []) {
            $groupByColumns = array_keys($template->column_filters);;
            $query->groupBy($groupByColumns);
        }

        if ($template->data_source == 'report_scanners') {
            $query->orderBy('center_no', 'ASC')
                ->orderBy('subject_code', 'ASC')
                ->orderBy('candidate_no', 'ASC');
        }else{
           $query->orderBy($primaryKey, "ASC");
        }
        $query->each(function (object $row) use ($fields, $template, &$processed, $limit) {
                if ($processed >=  $limit) {
                    return false; // Stops the iteration
                }
                $this->pdf->AddPage();
                foreach ($fields as  $field) {
                    $this->addElementToPdf($field, $row, $template);
                }
                $processed++;
            });




        // Explicitly set headers

        $this->pdf->Output("Scanners"  . ".pdf", "I");
        $this->pdf->Output("Scanners"  . ".pdf", "F");
        header("Content-type: application/pdf");
        header("Content-disposition: attectment; filename = Scanners" . '.pdf');
        readfile("Scanners" . ".pdf");
        unlink("Scanners" . '.pdf');
        exit;
        ob_end_flush();



        // return response()->streamDownload(function () use ($templateId, $filters, $values, $limit) {


        //      //Clear any potential buffers
        //     if (ob_get_level()) {
        //         ob_end_clean();
        //     }



        //     echo $this->pdf->Output('S');
        //     ob_end_flush();
        // },   $fileName, $headers);


        // return response()->streamDownload(
        //     function ()  use ($templateId, $filters, $values, $limit) {
        //         dd('ok');
        //         // Clear any potential buffers
        //         if (ob_get_level()) {
        //             ob_end_clean();
        //         }


        //     },
        //     'document.pdf'
        // );


        // return response()->streamDownload(function () {
        //     echo $this->pdf->Output('S');
        //     // $this->pdf->Output("Scanners"  . ".pdf", "I");
        //     // $this->pdf->Output("Scanners"  . ".pdf", "F");
        //     // header("Content-type: application/pdf");
        //     // header("Content-disposition: attectment; filename = Scanners" . '.pdf');
        //     // readfile("Scanners" . ".pdf");
        //     // unlink("Scanners" . '.pdf');
        //     exit;
        //     ob_end_flush();
        // }, 'large-document.pdf');
    }

    protected function formatFieldValue($value, $format)
    {
        if (empty($format)) return $value;

        switch ($format) {
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'currency':
                return '$' . number_format($value, 2);
            case 'percentage':
                return round($value * 100, 2) . '%';
            case 'uppercase':
                return strtoupper($value);
            case 'lowercase':
                return strtolower($value);
            default:
                return $value;
        }
    }


    // protected function renderTableRow($columns, $row, $widths)
    // {
    //     foreach ($columns as $column) {
    //         $value = $row[$column] ?? '';
    //         Fpdf::Cell($widths[$column], 6, $this->formatCellValue($value), 1);
    //     }
    //     Fpdf::Ln();
    // }

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



    protected function addElementToPdf($element, $data, $template = null)
    {


        // Set font if specified
        if ($element->font_family && $element->font_size) {
            $style = $element->font_style ?: '';
            $this->pdf->SetFont($element->font_family, $style, $element->font_size);
        }

        // Set color if specified
        if ($element->color) {
            list($r, $g, $b) = sscanf($element->color, "#%02x%02x%02x");
            $this->pdf->SetTextColor($r, $g, $b);
        }



        // Convert pixel position to mm for FPDF
        $elementX_px = $element->x_position;  // 100px from left
        $elementY_px = $element->y_position;   // 50px from top
        $width_px = $element->width;     // 200px width
        $height_px = $element->height;    // 100px height

        // Convert to mm
        $x_mm = px2mm($elementX_px);
        $y_mm = px2mm($elementY_px);
        $width_mm = px2mm($width_px);
        $height_mm = px2mm($height_px);


        // Handle different element types
        switch ($element->element_type) {
            case 'text':
                $content = $element->is_dynamic ? $data->{$element->data_columns} : $element->content;
                if ($element->rotation != 0 && $element->is_rotated) {
                    // Adjust position for rotated text to account for resizing
                    $rad = deg2rad($element->rotation);
                    $textWidth = $this->pdf->GetStringWidth($element->content);
                    $textHeight = $element->font_size / 2.8;

                    $adjX = $x_mm + sin($rad) *  $width_mm  / 2;
                    $adjY = $y_mm - cos($rad) *  $width_mm  / 2;

                    // $pdf->rotatedText($adjX, $adjY, $element->content, $element->rotation);

                    $this->pdf->Rotate($this->antiClockwiseToClockwise($element->rotation),  $adjX,  $adjY);
                    $this->pdf->Text($adjX, $adjY, $content);
                    $this->pdf->Rotate(0);
                } else {
                    $this->pdf->SetXY($x_mm, $y_mm);
                    $this->pdf->Cell($width_mm, $height_mm, $content, 0, 0, $element->alignment);
                }
                break;
            case 'barcode':
                $content = $element->is_dynamic ? $data->{$element->data_columns} : $element->content;
                $this->pdf->Code39($x_mm, $y_mm, $element->content, $content);
                break;
            case 'image':
                $content = $element->is_dynamic ? $data->{$element->data_columns} : $element->content;
                // For images, content would be the path
                $this->pdf->SetXY($x_mm, $y_mm);
                $this->pdf->Image($content, $element->x_position, $element->y_position,  $width_px, $element->height);
                break;
            case 'table':
                //    $content = $element->is_dynamic ? $data->{$element->data_columns} : $element->content;
                $this->pdf->SetXY($x_mm+7, $y_mm);
                $this->addTableToPdf($template, $data, $element);
                break;
        }
    }


    // public function convertPdfToImage($pdfPath, $outputPath, $width = null, $height = null)
    // {
    //     $imagick = new Imagick();

    //     // Set resolution (DPI) - higher DPI means better quality but larger file size
    //     $imagick->setResolution(300, 300);

    //     // Read the PDF file
    //     $imagick->readImage($pdfPath);

    //     // Set the output format
    //     $imagick->setImageFormat('jpg');

    //     // If width or height is specified, resize the image while maintaining aspect ratio
    //     if ($width || $height) {
    //         $imagick->scaleImage($width ?: 0, $height ?: 0, true);
    //     }

    //     // If you want just the first page
    //     $imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

    //     // Save the image
    //     $imagick->writeImage($outputPath);

    //     // Clean up
    //     $imagick->clear();
    //     $imagick->destroy();

    //     return $outputPath;
    // }


    protected function antiClockwiseToClockwise($angle)
    {
        // Convert anti-clockwise angle to clockwise
        return (360 + $angle) % 360;
    }


    protected function addTableToPdf($template, $data, $element)
    {

        $columns = $element->data_columns ? explode(',', $element->data_columns) : [];

        if (empty($columns)) return;
        // Build query with filters 190683821


        $query = DB::table($template->data_source)->select('*');

        if ($element->filters) {
            $filters = json_decode($element->filters, true);
            $this->applyFilters($query, $filters, $data);
        }

        $width = px2mm($element->width);
        // dd($width );
        $data = $query->get();



        $colWidth =  $width / count($columns);

        $isEmpty = function ($value) {
            return $value === null || $value === '' || $value === [];
        };

        $collection  = $data;

        $keysToShift = ['component_result', 'component_description'];
        $rowsToRemove = [];
        $rowFilters = 'subject_code';
        $data = $collection->map(function ($item, $key) use ($collection, $rowFilters, $keysToShift, $isEmpty, &$rowsToRemove) {
            foreach ($keysToShift as $field) {
                if ($isEmpty($item->{$field})) {
                    // Find next non-empty value
                    for ($i = $key + 1; $i < $collection->count(); $i++) {
                        if (!$isEmpty($collection[$i]->{$field}) && $collection[$i]->{$rowFilters} == $item->{$rowFilters}) {
                            $item->$field = $collection[$i]->{$field};
                            $rowsToRemove[$i] = true; // Mark this row for removal
                            break;
                        }
                    }
                }
            }
            return $item;
        });


        $data  = $data->reject(function ($item, $key) use ($rowsToRemove) {
            return isset($rowsToRemove[$key]);
        })->values();


        // Data
        $this->pdf->SetFont($element->font_family, '', $element->font_size);
        $this->pdf->SetWidths(array(60, 65, 32, 32));
        $this->pdf->SetAligns(array('L', 'L', 'C', 'C'));
        $this->renderTableRow($data, $columns);
    }

    // Render table row
    protected function renderTableRow($rows, $columns)
    {
        foreach ($rows as $row) {
            $rowCells = array();
            foreach ($columns as $column) {
                array_push($rowCells, $row->{$column} ?? '');
            }
            $this->pdf->Row($rowCells);
        }
    }


    protected function applyFilters($query, $filters, $data)
    {
        foreach ($filters as $filter) {
            if (empty($filter['column']) || !isset($filter['operator'])) continue;
            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $data->{$column};
            switch ($operator) {
                case 'equals':
                    $query->where($column, $value);
                    break;
                case 'not_equals':
                    $query->where($column, '!=', $value);
                    break;
                case 'contains':
                    $query->where($column, 'LIKE', "%{$value}%");
                    break;
                case 'starts_with':
                    $query->where($column, 'LIKE', "{$value}%");
                    break;
                case 'ends_with':
                    $query->where($column, 'LIKE', "%{$value}");
                    break;
                case 'greater':
                    $query->where($column, '>', $value);
                    break;
                case 'less':
                    $query->where($column, '<', $value);
                    break;
                case 'greater_or_equal':
                    $query->where($column, '>=', $value);
                    break;
                case 'less_or_equal':
                    $query->where($column, '<=', $value);
                    break;
                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween($column, $value);
                    }
                    break;
                case 'in':
                    if (is_array($value)) {
                        $query->whereIn($column, $value);
                    }
                    break;
                case 'not_in':
                    if (is_array($value)) {
                        $query->whereNotIn($column, $value);
                    }
                    break;
                case 'null':
                    $query->whereNull($column);
                    break;
                case 'not_null':
                    $query->whereNotNull($column);
                    break;
            }
        }
    }









    protected  function formatValue($value, $format)
    {
        if (empty($format)) return $value;

        switch ($format) {
            case 'currency':
                return '$' . number_format($value, 2);
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'datetime':
                return date('Y-m-d H:i', strtotime($value));
            case 'percentage':
                return round($value * 100) . '%';
            default:
                return $value;
        }
    }
}
