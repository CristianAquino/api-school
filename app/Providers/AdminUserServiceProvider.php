<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AdminUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        if (!User::where('email', 'admin@admin.com')->exists()) {
            // create admin
            $admin = Admin::create([
                'role' => 'ROLE_SUPERADMIN'
            ]);

            $admin->user()->create([
                'name' => 'administrator',
                'email' => 'admin@admin.com',
                'password' => '12345678',
                'code' => 'AD' . (int) date("Y") * 10000,
            ]);
        }
    }
}
