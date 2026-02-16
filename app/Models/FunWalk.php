<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunWalk extends Model
{
    use HasFactory;

    protected $table = 'fun_walks';

    protected $fillable = [
        'title',
        'date',
        'location',
        'price',
        'description',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Scope to get active fun walks
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**     * Relationship: A fun walk has many registrations
     */
    public function registrations()
    {
        return $this->hasMany(FunWalkRegistration::class);
    }

    /**     * Accessor for formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format((float) $this->price, 2);
    }
}
