<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // صلاحيات المنتجات
            ['name' => 'products.create', 'description' => 'إنشاء منتج جديد', 'module' => 'products', 'action' => 'create'],
            ['name' => 'products.read', 'description' => 'عرض المنتجات', 'module' => 'products', 'action' => 'read'],
            ['name' => 'products.update', 'description' => 'تعديل المنتجات', 'module' => 'products', 'action' => 'update'],
            ['name' => 'products.delete', 'description' => 'حذف المنتجات', 'module' => 'products', 'action' => 'delete'],

            // صلاحيات الفئات
            ['name' => 'categories.create', 'description' => 'إنشاء فئة جديدة', 'module' => 'categories', 'action' => 'create'],
            ['name' => 'categories.read', 'description' => 'عرض الفئات', 'module' => 'categories', 'action' => 'read'],
            ['name' => 'categories.update', 'description' => 'تعديل الفئات', 'module' => 'categories', 'action' => 'update'],
            ['name' => 'categories.delete', 'description' => 'حذف الفئات', 'module' => 'categories', 'action' => 'delete'],

            // صلاحيات الطلبات
            ['name' => 'orders.create', 'description' => 'إنشاء طلب جديد', 'module' => 'orders', 'action' => 'create'],
            ['name' => 'orders.read', 'description' => 'عرض الطلبات', 'module' => 'orders', 'action' => 'read'],
            ['name' => 'orders.update', 'description' => 'تعديل الطلبات', 'module' => 'orders', 'action' => 'update'],
            ['name' => 'orders.delete', 'description' => 'حذف الطلبات', 'module' => 'orders', 'action' => 'delete'],
            ['name' => 'orders.process', 'description' => 'معالجة الطلبات', 'module' => 'orders', 'action' => 'process'],
            ['name' => 'orders.refund', 'description' => 'استرداد الطلبات', 'module' => 'orders', 'action' => 'refund'],

            // صلاحيات العملاء
            ['name' => 'customers.create', 'description' => 'إنشاء عميل جديد', 'module' => 'customers', 'action' => 'create'],
            ['name' => 'customers.read', 'description' => 'عرض العملاء', 'module' => 'customers', 'action' => 'read'],
            ['name' => 'customers.update', 'description' => 'تعديل العملاء', 'module' => 'customers', 'action' => 'update'],
            ['name' => 'customers.delete', 'description' => 'حذف العملاء', 'module' => 'customers', 'action' => 'delete'],

            // صلاحيات المستخدمين
            ['name' => 'users.create', 'description' => 'إنشاء مستخدم جديد', 'module' => 'users', 'action' => 'create'],
            ['name' => 'users.read', 'description' => 'عرض المستخدمين', 'module' => 'users', 'action' => 'read'],
            ['name' => 'users.update', 'description' => 'تعديل المستخدمين', 'module' => 'users', 'action' => 'update'],
            ['name' => 'users.delete', 'description' => 'حذف المستخدمين', 'module' => 'users', 'action' => 'delete'],

            // صلاحيات الفروع
            ['name' => 'branches.create', 'description' => 'إنشاء فرع جديد', 'module' => 'branches', 'action' => 'create'],
            ['name' => 'branches.read', 'description' => 'عرض الفروع', 'module' => 'branches', 'action' => 'read'],
            ['name' => 'branches.update', 'description' => 'تعديل الفروع', 'module' => 'branches', 'action' => 'update'],
            ['name' => 'branches.delete', 'description' => 'حذف الفروع', 'module' => 'branches', 'action' => 'delete'],

            // صلاحيات التقارير
            ['name' => 'reports.sales', 'description' => 'تقارير المبيعات', 'module' => 'reports', 'action' => 'sales'],
            ['name' => 'reports.inventory', 'description' => 'تقارير المخزون', 'module' => 'reports', 'action' => 'inventory'],
            ['name' => 'reports.financial', 'description' => 'التقارير المالية', 'module' => 'reports', 'action' => 'financial'],

            // صلاحيات الإعدادات
            ['name' => 'settings.general', 'description' => 'الإعدادات العامة', 'module' => 'settings', 'action' => 'general'],
            ['name' => 'settings.system', 'description' => 'إعدادات النظام', 'module' => 'settings', 'action' => 'system'],

            // صلاحيات النقدية
            ['name' => 'cash.open', 'description' => 'فتح الدرج النقدي', 'module' => 'cash', 'action' => 'open'],
            ['name' => 'cash.close', 'description' => 'إغلاق الدرج النقدي', 'module' => 'cash', 'action' => 'close'],
            ['name' => 'cash.adjust', 'description' => 'تعديل النقدية', 'module' => 'cash', 'action' => 'adjust'],

            // صلاحيات الخصومات
            ['name' => 'discounts.apply', 'description' => 'تطبيق الخصومات', 'module' => 'discounts', 'action' => 'apply'],
            ['name' => 'discounts.manage', 'description' => 'إدارة الخصومات', 'module' => 'discounts', 'action' => 'manage'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
