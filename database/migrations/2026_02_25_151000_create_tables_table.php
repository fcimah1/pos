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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('number')->unique(); // رقم الطاولة
            $table->integer('capacity')->default(4); // السعة القصوى
            $table->boolean('is_available')->default(true); // هل متاحة
            $table->boolean('is_active')->default(true); // هل نشطة
            $table->timestamps();
            
            $table->index(['branch_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
