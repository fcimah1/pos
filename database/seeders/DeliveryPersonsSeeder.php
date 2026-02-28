<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryPersonsSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->where('code', 'HQ')->value('id') ?: 1;

        $dps = [
            ['name' => 'Mohamed', 'phone' => '01000000001'],
            ['name' => 'Ali', 'phone' => '01000000002'],
        ];

        foreach ($dps as $d) {
            DB::table('delivery_persons')->updateOrInsert([
                'branch_id' => $branchId, 'phone' => $d['phone']
            ], array_merge($d, ['branch_id' => $branchId, 'is_active' => true]));
        }
    }
}
