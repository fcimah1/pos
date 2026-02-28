<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('branches')->updateOrInsert([
            'code' => 'HQ'
        ], [
            'name' => 'Headquarters',
            'code' => 'HQ',
            'address' => 'Main Street',
            'phone' => '000-000-0000',
            'email' => 'hq@example.com',
            'tax_rate' => 5.00,
            'is_active' => true,
        ]);
    }
}
