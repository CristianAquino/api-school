<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Admin::truncate();

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
