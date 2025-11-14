<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InvitationScriptFee extends Model
{
    use HasFactory;

    protected $table = 'invitation_script_fee';
    protected $primaryKey = 'component_code';
    public $incrementing = false;   // because it's not auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'component_code',
        'subject_code',
        'component_no',
        'session',
        'financial_year',
        'script_fee',
    ];





    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code');
    }

    // Automatically generate padded component_code
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->component_code =
                str_pad($model->subject_code, 4, '0', STR_PAD_LEFT) .
                str_pad($model->component_no, 2, '0', STR_PAD_LEFT);
        });

        static::updating(function ($model) {
            $model->component_code =
                str_pad($model->subject_code, 4, '0', STR_PAD_LEFT) .
                str_pad($model->component_no, 2, '0', STR_PAD_LEFT);
        });
    }
}
