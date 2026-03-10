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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Pre-populate with company settings
        $settings = [
            ['key' => 'company_name', 'value' => 'نظام POS المطور'],
            ['key' => 'company_phone_1', 'value' => null],
            ['key' => 'company_phone_2', 'value' => null],
            ['key' => 'company_phone_3', 'value' => null],
            ['key' => 'company_address', 'value' => null],
            ['key' => 'company_logo', 'value' => null],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
            \Illuminate\Support\Facades\DB::table('settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
