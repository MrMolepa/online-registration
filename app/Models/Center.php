<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    protected $table = "centers";
    protected $primaryKey = 'center_no';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'center_no',
        'center_full_name',
        'center_name',
        'district',
        'district_code',
        'address',
        'level',
        'sessions',
        'district_address'
    ];




    public function candidates()
    {
        return $this->hasMany(CenterCandidate::class, 'center_no', 'center_no');
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'valid_center_subject','center_no','subject_code');
    }


    public function users()
    {

        return $this->hasMany(User::class, 'center_no', 'center_no');
    }

    public function bankStatements()
    {
        return $this->has(BankStatement::class);
    }

    public function otherCharge()
    {
        return $this->has(CenterOtherCharge::class);
    }

    public function levels()
    {
        return $this->belongsToMany(Level::class, 'center_level', 'center_id', 'level_id',);
    }
}
