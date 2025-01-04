<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        User::truncate();
        Teacher::truncate();
        $faker = Faker::create();

        $password = Hash::make('12345678');

        // create admin
        $admin = Admin::create([
            'code_admin' => 'AD' . (int) date("Y") * 10000,
            'role' => 'ROLE_SUPERADMIN'
        ]);

        $admin->user()->create([
            'name' => 'administrator',
            'email' => 'admin@admin.com',
            'password' => $password,
        ]);

        // create teacher
        for ($i = 0; $i < 15; $i++) {
            # code...
            $code = (int) date("Y") * 10000 + $i;
            $teacher = Teacher::create([
                'code_teacher' => 'TE' . $code,
                'role' => 'ROLE_TEACHER'
            ]);
            $teacher->user()->create([
                'name' => $faker->name,
                'first_name' => $faker->firstName,
                'second_name' => $faker->lastName,
                'birth_date' => $faker->date('Y-m-d', '1990-12-31'),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'dni' => (string)$faker->randomNumber(8),
                'email' => $faker->email,
                'password' => $password,
            ]);
        }
    }
}
