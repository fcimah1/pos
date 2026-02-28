<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->where('code', 'HQ')->value('id') ?: 1;

        $cats = [
            ['name' => 'وجبات رئيسية', 'slug' => 'food',      'branch_id' => $branchId],
            ['name' => 'مشروبات',       'slug' => 'drinks',    'branch_id' => $branchId],
            ['name' => 'حلويات',        'slug' => 'desserts',  'branch_id' => $branchId],
            ['name' => 'مقبلات',        'slug' => 'starters',  'branch_id' => $branchId],
            ['name' => 'بيتزا',         'slug' => 'pizza',     'branch_id' => $branchId],
            ['name' => 'وجبات سريعة',   'slug' => 'fastfood',  'branch_id' => $branchId],
            ['name' => 'سلطات',         'slug' => 'salads',    'branch_id' => $branchId],
            ['name' => 'إفطار',         'slug' => 'breakfast', 'branch_id' => $branchId],
        ];

        foreach ($cats as $c) {
            DB::table('categories')->updateOrInsert(
                ['branch_id' => $c['branch_id'], 'slug' => $c['slug']],
                array_merge($c, ['is_active' => true])
            );
        }
    }
}
