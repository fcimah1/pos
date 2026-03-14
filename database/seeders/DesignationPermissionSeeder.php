<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على معرفات الوظائف
        $designations = DB::table('designations')->pluck('id', 'name');
        
        // الحصول على معرفات الصلاحيات
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $designationPermissions = [];

        // مدير النظام - لديه كل الصلاحيات
        if (isset($designations['مدير نظام'])) {
            foreach ($permissions as $permissionId) {
                $designationPermissions[] = [
                    'designation_id' => $designations['مدير نظام'],
                    'permission_id' => $permissionId,
                ];
            }
        }

        // مدير الفرع - صلاحيات إدارة الفرع والمبيعات والتقارير
        if (isset($designations['مدير فرع'])) {
            $branchManagerPermissions = [
                'products.read', 'products.update',
                'categories.read', 'categories.update',
                'orders.create', 'orders.read', 'orders.update', 'orders.process',
                'customers.create', 'customers.read', 'customers.update',
                'users.create', 'users.read', 'users.update',
                'branches.read',
                'reports.sales', 'reports.inventory', 'reports.financial',
                'settings.general',
                'cash.open', 'cash.close', 'cash.adjust',
                'discounts.apply', 'discounts.manage',
            ];

            foreach ($branchManagerPermissions as $permissionName) {
                if (isset($permissions[$permissionName])) {
                    $designationPermissions[] = [
                        'designation_id' => $designations['مدير فرع'],
                        'permission_id' => $permissions[$permissionName],
                    ];
                }
            }
        }

        // الكاشير - صلاحيات المبيعات الأساسية
        if (isset($designations['كاشير'])) {
            $cashierPermissions = [
                'products.read',
                'categories.read',
                'orders.create', 'orders.read', 'orders.update',
                'customers.create', 'customers.read',
                'orders.process',
                'reports.sales',
                'discounts.apply',
            ];

            foreach ($cashierPermissions as $permissionName) {
                if (isset($permissions[$permissionName])) {
                    $designationPermissions[] = [
                        'designation_id' => $designations['كاشير'],
                        'permission_id' => $permissions[$permissionName],
                    ];
                }
            }
        }

        // إدخال الصلاحيات في قاعدة البيانات
        foreach ($designationPermissions as $dp) {
            DB::table('designation_permissions')->updateOrInsert(
                [
                    'designation_id' => $dp['designation_id'],
                    'permission_id' => $dp['permission_id'],
                ],
                $dp
            );
        }
    }
}
