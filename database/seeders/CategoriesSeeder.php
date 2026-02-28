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
            ['name' => 'Food', 'slug' => 'food', 'branch_id' => $branchId],
            ['name' => 'Drinks', 'slug' => 'drinks', 'branch_id' => $branchId],
        ];

        foreach ($cats as $c) {
            DB::table('categories')->updateOrInsert([
                'branch_id' => $c['branch_id'], 'slug' => $c['slug']
            ], array_merge($c, ['is_active' => true]));
        }
    }
}
