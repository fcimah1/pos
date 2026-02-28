<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'branch_id',
        'number',
        'capacity',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_number', 'number')
            ->where('branch_id', $this->branch_id);
    }

    // التحقق مما إذا كانت الطاولة مشغولة حالياً
    public function isOccupied(): bool
    {
        return $this->orders()
            ->whereIn('status', ['open', 'suspended'])
            ->exists();
    }

    // الحصول على الطاولات المتاحة فقط
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_active', true)
            ->whereDoesntHave('orders', function ($q) {
                $q->whereIn('status', ['open', 'suspended']);
            });
    }
}
