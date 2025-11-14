<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplateBlock  extends Model
{
    use HasFactory;

    protected $table = "pdf_template_blocks";


    protected $fillable = [
        'template_id',
        'element_type',
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
        'data_columns',
        'filters'
    ];





}
