<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->decimal('delivery_charge', 10, 2)->default(0)->after('type');
            $table->string('floor_number')->nullable()->after('address_line_2');
            $table->string('apartment_number')->nullable()->after('floor_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['delivery_charge', 'floor_number', 'apartment_number']);
        });
    }
};
