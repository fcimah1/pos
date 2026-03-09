<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Run migrations and seed data automatically if the database is empty
        // This ensures the standalone .exe works offline immediately after installation
        try {
            // Always run migrations to ensure the schema is up to date
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            // Seed only if the users table is empty
            if (\Illuminate\Support\Facades\DB::table('users')->count() === 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('NativeApp SQLite Auto-Migration Failed: ' . $e->getMessage());
        }

        Window::open();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
