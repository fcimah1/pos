<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryPerson extends Model
{
    protected $table = 'delivery_persons';

    protected $fillable = [
        'branch_id',
        'name',
        'phone',
        'email',
        'vehicle_type',
        'vehicle_number',
        'latitude',
        'longitude',
        'status',
        'rating',
        'total_deliveries',
        'is_active',
        'last_location_update',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'last_location_update' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}