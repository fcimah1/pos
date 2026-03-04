<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة حالات التجهيز والتوصيل إلى ENUM للحقل status
        if (Schema::hasTable('orders')) {
            DB::statement("
                ALTER TABLE `orders`
                MODIFY COLUMN `status`
                ENUM('open','suspended','preparing','delivering','paid','completed','cancelled')
                NOT NULL DEFAULT 'open'
            ");
        }
    }

    public function down(): void
    {
        // الرجوع إلى القيم السابقة بدون التجهيز/التوصيل
        if (Schema::hasTable('orders')) {
            DB::statement("
                ALTER TABLE `orders`
                MODIFY COLUMN `status`
                ENUM('open','suspended','paid','completed','cancelled')
                NOT NULL DEFAULT 'open'
            ");
        }
    }
};
