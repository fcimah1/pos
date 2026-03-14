<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'description', 'module', 'action'];

    /**
     * الصلاحيات تنتمي إلى عدة وظائف
     */
    public function designations(): BelongsToMany
    {
        return $this->belongsToMany(Designation::class, 'designation_permissions');
    }
}
