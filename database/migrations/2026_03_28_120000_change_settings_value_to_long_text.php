<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Base64 logos exceed MySQL TEXT (64KB). LongText allows ~4GB.
     * SQLite treats TEXT flexibly; change is MySQL/MariaDB only.
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE settings MODIFY `value` LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE settings MODIFY `value` TEXT NULL');
        }
    }
};
