<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Designation extends Model
{
    protected $fillable = ['name', 'hierarchy_level'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * الوظيفة لديها عدة صلاحيات
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'designation_permissions');
    }

    /**
     * التحقق مما إذا كانت الوظيفة لديها صلاحية معينة
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }
}
