<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->where('code', 'HQ')->value('id') ?: 1;
        $roleId = DB::table('roles')->where('name', 'admin')->value('id') ?: 1;

        DB::table('users')->updateOrInsert([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make(env('ADMIN_PASSWORD', '123456')),
            'branch_id' => $branchId,
            'role_id' => $roleId,
            'is_active' => true,
        ]);
    }
}
