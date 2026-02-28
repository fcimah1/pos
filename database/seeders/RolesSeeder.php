<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'cashier', 'description' => 'POS Cashier'],
            ['name' => 'chef', 'description' => 'Kitchen staff'],
            ['name' => 'manager', 'description' => 'Branch manager'],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(['name' => $r['name']], $r);
        }
    }
}
