<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // التحقق من وجود الفرع
        $branchId = DB::table('branches')->first()?->id ?? 1;
        
        // إنشاء طاولات من 1 إلى 20
        for ($i = 1; $i <= 20; $i++) {
            DB::table('tables')->insert([
                'branch_id' => $branchId,
                'number' => (string)$i,
                'capacity' => 4,
                'is_available' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
