<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeGroup extends Model
{
    use HasFactory;
    protected $table = "fee_groups";

    protected $fillable = [
        'name',
        'description',
        'session_id',
        'level_id',
        'created_at',
        'updated_at'
    ];
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public function feetypes()
    {
        return $this->belongsToMany(FeeType::class,'fee_group_details', 'fee_group_id','fee_type_id')->withPivot('amount','id' ,'fee_type_id', 'key_type', 'subject_code', 'option_code', 'component_code');
    }


    public function cloneWithFeetypes()
    {
        $clone = $this->replicate();
        $clone->push();

        // Clone related tasks
        foreach ($this->feetypes as $feetype) {
            $clonedFeetype = $feetype->replicate();
            $clone->feetypes()->save( $clonedFeetype);
        }
        return $clone;
    }



}
