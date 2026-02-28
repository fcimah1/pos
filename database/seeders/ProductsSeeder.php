<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->where('code', 'HQ')->value('id') ?: 1;
        $foodCat = DB::table('categories')->where('branch_id', $branchId)->where('slug', 'food')->value('id');
        $drinksCat = DB::table('categories')->where('branch_id', $branchId)->where('slug', 'drinks')->value('id');

        $products = [
            ['branch_id' => $branchId, 'category_id' => $foodCat, 'name' => 'Pizza', 'price' => 50.00, 'barcode' => '0001'],
            ['branch_id' => $branchId, 'category_id' => $foodCat, 'name' => 'Burger', 'price' => 30.00, 'barcode' => '0002'],
            ['branch_id' => $branchId, 'category_id' => $drinksCat, 'name' => 'Cola', 'price' => 5.00, 'barcode' => '0003'],
        ];

        foreach ($products as $p) {
            DB::table('products')->updateOrInsert([
                'branch_id' => $p['branch_id'], 'barcode' => $p['barcode']
            ], array_merge($p, ['is_active' => true]));
        }
    }
}
