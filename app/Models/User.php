<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'role_id',
        'designation_id',
        'department_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    protected $appends = ['is_admin', 'permissions_count'];

    public function getIsAdminAttribute(): bool
    {
        return $this->isSystemAdmin();
    }

    public function getPermissionsCountAttribute(): int
    {
        return $this->designation ? $this->designation->permissions()->count() : 0;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if user has role by name (simple helper using role relation).
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && strcasecmp($this->role->name, $roleName) === 0;
    }

    /**
     * التحقق مما إذا كان المستخدم لديه صلاحية معينة عبر وظيفته
     */
    public function hasPermission(string $permissionName): bool
    {
        // مدير النظام لديه كل الصلاحيات
        if ($this->designation && $this->designation->name === 'مدير نظام') {
            return true;
        }

        return $this->designation && $this->designation->hasPermission($permissionName);
    }

    /**
     * التحقق مما إذا كان المستخدم مدير نظام
     */
    public function isSystemAdmin(): bool
    {
        return $this->designation && $this->designation->name === 'مدير نظام';
    }
}
