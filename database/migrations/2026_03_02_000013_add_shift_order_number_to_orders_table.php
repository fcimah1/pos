<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shift_order_number')->nullable()->after('order_number');
            $table->index(['branch_id', 'shift_id', 'shift_order_number']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'shift_id', 'shift_order_number']);
            $table->dropColumn('shift_order_number');
        });
    }
};
