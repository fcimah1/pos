<?php

namespace App\Services;

use App\Models\User;
use App\Models\Designation;
use App\Models\Permission;

class PermissionService
{
    /**
     * التحقق مما إذا كان المستخدم لديه صلاحية معينة
     */
    public function userHasPermission(User $user, string $permissionName): bool
    {
        return $user->hasPermission($permissionName);
    }

    /**
     * التحقق مما إذا كانت الوظيفة لديها صلاحية معينة
     */
    public function designationHasPermission(Designation $designation, string $permissionName): bool
    {
        return $designation->hasPermission($permissionName);
    }

    /**
     * الحصول على كل صلاحيات المستخدم
     */
    public function getUserPermissions(User $user): array
    {
        if ($user->isSystemAdmin()) {
            return Permission::pluck('description')->toArray();
        }

        return $user->designation?->permissions()->pluck('description')->toArray() ?? [];
    }

    /**
     * الحصول على صلاحيات الوظيفة حسب الوحدة
     */
    public function getDesignationPermissionsByModule(Designation $designation, string $module): array
    {
        return $designation->permissions()
            ->where('module', $module)
            ->pluck('action')
            ->toArray();
    }

    /**
     * التحقق من صلاحيات متعددة
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->userHasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من كل الصلاحيات المطلوبة
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->userHasPermission($user, $permission)) {
                return false;
            }
        }

        return true;
    }
}
