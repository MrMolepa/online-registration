<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateLBSE extends Model
{
    use HasFactory;

    protected $table = "lbse_results";
    public $timestamps = false;

    // protected $fillable = [
    //     'center_no',
    //     'center_name',
    //     'district',
    // ];
}
