<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['id' => 1, 'name' => 'مدير نظام', 'hierarchy_level' => 1],
            ['id' => 2, 'name' => 'كاشير', 'hierarchy_level' => 5],
            ['id' => 4, 'name' => 'مدير فرع', 'hierarchy_level' => 2],
        ];

        foreach ($designations as $designation) {
            DB::table('designations')->updateOrInsert(
                ['id' => $designation['id']],
                $designation
            );
        }
    }
}
