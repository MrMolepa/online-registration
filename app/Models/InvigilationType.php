<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationType extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_types';
    protected $fillable = [
        'invigilation_catergories_id',
        'name',
    ];
    public function invigilation_catergories()
    {
        return $this->belongsTo(InvigilationCatergories::class, 'invigilation_catergories_id', 'id');
    }
}
