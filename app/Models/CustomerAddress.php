<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'address_line_1',
        'address_line_2',
        'floor_number',
        'apartment_number',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'delivery_charge',
        'notes',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'delivery_charge' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo   
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_address_id');
    }
}
