<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    use HasFactory;

    protected $table = "pdf_templates";


    protected $fillable = [
        'name',
        'description',
        'data_source',
        'orientation',
        'thumbnail',
        'category_id',
        'column_filters',
        'is_table_filters',
        'thumbnail',
        'is_blank',
    ];


    protected $casts = [
        'column_filters' => 'array',
      ];


}
