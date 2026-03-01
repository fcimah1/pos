<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->where('code', 'HQ')->value('id') ?: 1;

        // جلب IDs الفئات
        $cat = fn(string $slug) => DB::table('categories')
            ->where('branch_id', $branchId)
            ->where('slug', $slug)
            ->value('id');

        $products = [
            // ---- وجبات رئيسية ----
            ['slug' => 'food', 'name' => 'شاورما دجاج',       'price' => 35.00, 'barcode' => 'P001'],
            ['slug' => 'food', 'name' => 'كباب مشكل',         'price' => 55.00, 'barcode' => 'P002'],
            ['slug' => 'food', 'name' => 'دجاج مشوي',         'price' => 45.00, 'barcode' => 'P003'],
            ['slug' => 'food', 'name' => 'سمك مقلي',          'price' => 60.00, 'barcode' => 'P004'],
            ['slug' => 'food', 'name' => 'ستيك لحم',          'price' => 80.00, 'barcode' => 'P005'],

            // ---- بيتزا ----
            ['slug' => 'pizza', 'name' => 'بيتزا مارغريتا',   'price' => 50.00, 'barcode' => 'P010'],
            ['slug' => 'pizza', 'name' => 'بيتزا دجاج BBQ',   'price' => 65.00, 'barcode' => 'P011'],
            ['slug' => 'pizza', 'name' => 'بيتزا خضار',       'price' => 55.00, 'barcode' => 'P012'],
            ['slug' => 'pizza', 'name' => 'بيتزا لحم',        'price' => 70.00, 'barcode' => 'P013'],
            ['slug' => 'pizza', 'name' => 'بيتزا مأكولات بحرية', 'price' => 75.00, 'barcode' => 'P014'],

            // ---- وجبات سريعة ----
            ['slug' => 'fastfood', 'name' => 'برجر دجاج',     'price' => 30.00, 'barcode' => 'P020'],
            ['slug' => 'fastfood', 'name' => 'برجر لحم',      'price' => 40.00, 'barcode' => 'P021'],
            ['slug' => 'fastfood', 'name' => 'داندر ويتش',    'price' => 25.00, 'barcode' => 'P022'],
            ['slug' => 'fastfood', 'name' => 'هوت دوج',       'price' => 20.00, 'barcode' => 'P023'],
            ['slug' => 'fastfood', 'name' => 'بطاطس محمرة',   'price' => 15.00, 'barcode' => 'P024'],

            // ---- مقبلات ----
            ['slug' => 'starters', 'name' => 'حمص',           'price' => 10.00, 'barcode' => 'P030'],
            ['slug' => 'starters', 'name' => 'متبل',          'price' => 10.00, 'barcode' => 'P031'],
            ['slug' => 'starters', 'name' => 'فتوش',          'price' => 12.00, 'barcode' => 'P032'],
            ['slug' => 'starters', 'name' => 'سبرينج رول',    'price' => 18.00, 'barcode' => 'P033'],
            ['slug' => 'starters', 'name' => 'حلقات بصل',     'price' => 15.00, 'barcode' => 'P034'],

            // ---- سلطات ----
            ['slug' => 'salads', 'name' => 'سلطة خضراء',      'price' => 12.00, 'barcode' => 'P040'],
            ['slug' => 'salads', 'name' => 'سلطة سيزر',       'price' => 20.00, 'barcode' => 'P041'],
            ['slug' => 'salads', 'name' => 'سلطة يونانية',    'price' => 18.00, 'barcode' => 'P042'],
            ['slug' => 'salads', 'name' => 'سلطة تونة',       'price' => 22.00, 'barcode' => 'P043'],

            // ---- مشروبات ----
            ['slug' => 'drinks', 'name' => 'كولا',            'price' => 8.00,  'barcode' => 'P050'],
            ['slug' => 'drinks', 'name' => 'عصير برتقال',     'price' => 12.00, 'barcode' => 'P051'],
            ['slug' => 'drinks', 'name' => 'عصير مانجو',      'price' => 15.00, 'barcode' => 'P052'],
            ['slug' => 'drinks', 'name' => 'ليمون بالنعناع',  'price' => 12.00, 'barcode' => 'P053'],
            ['slug' => 'drinks', 'name' => 'قهوة عربية',      'price' => 10.00, 'barcode' => 'P054'],
            ['slug' => 'drinks', 'name' => 'شاي بالنعناع',    'price' => 8.00,  'barcode' => 'P055'],
            ['slug' => 'drinks', 'name' => 'كابتشينو',        'price' => 18.00, 'barcode' => 'P056'],
            ['slug' => 'drinks', 'name' => 'ماء معدني',       'price' => 5.00,  'barcode' => 'P057'],

            // ---- حلويات ----
            ['slug' => 'desserts', 'name' => 'كنافة',         'price' => 20.00, 'barcode' => 'P060'],
            ['slug' => 'desserts', 'name' => 'أم علي',        'price' => 18.00, 'barcode' => 'P061'],
            ['slug' => 'desserts', 'name' => 'تشيز كيك',      'price' => 25.00, 'barcode' => 'P062'],
            ['slug' => 'desserts', 'name' => 'براونيز',       'price' => 20.00, 'barcode' => 'P063'],
            ['slug' => 'desserts', 'name' => 'بودينج فانيليا', 'price' => 15.00, 'barcode' => 'P064'],
            ['slug' => 'desserts', 'name' => 'جيلاتو',        'price' => 22.00, 'barcode' => 'P065'],

            // ---- إفطار ----
            ['slug' => 'breakfast', 'name' => 'فول مدمس',     'price' => 10.00, 'barcode' => 'P070'],
            ['slug' => 'breakfast', 'name' => 'بيض بالطماطم', 'price' => 12.00, 'barcode' => 'P071'],
            ['slug' => 'breakfast', 'name' => 'فطيرة جبنة',   'price' => 15.00, 'barcode' => 'P072'],
            ['slug' => 'breakfast', 'name' => 'بانكيك',        'price' => 18.00, 'barcode' => 'P073'],
            ['slug' => 'breakfast', 'name' => 'توست بالعسل',  'price' => 10.00, 'barcode' => 'P074'],
        ];

        foreach ($products as $p) {
            $categoryId = $cat($p['slug']);
            if (!$categoryId) continue;

            $productId = DB::table('products')->insertGetId([
                'branch_id'   => $branchId,
                'category_id' => $categoryId,
                'name'        => $p['name'],
                'price'       => 0, // Legacy column
                'barcode'     => $p['barcode'],
                'is_active'   => true,
            ]);

            DB::table('product_variations')->insert([
                'product_id' => $productId,
                'size_name'  => 'عادي',
                'price'      => $p['price'],
                'barcode'    => $p['barcode'],
            ]);
        }
    }
}
