<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplateCategory extends Model
{
    use HasFactory;

    protected $table = "pdf_template_categories";


    protected $fillable = [

        'name',
        'description',
        'parent_id',
    ];


    public function children()
    {
        return $this->hasMany(PdfTemplateCategory::class, 'parent_id', 'id');
    }
}
