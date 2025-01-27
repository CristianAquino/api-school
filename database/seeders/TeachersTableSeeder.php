<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;


class TeachersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Teacher::truncate();
        $faker = Faker::create();

        // $password = Hash::make('12345678');

        // create teacher
        for ($i = 0; $i < 15; $i++) {
            # code...
            $od = (int) date("Y") * 10000 + $i;
            $code = 'TE' . $od;
            $dni = (string)$faker->randomNumber(8, true);
            $teacher = Teacher::create([
                'role' => 'ROLE_TEACHER'
            ]);
            $teacher->user()->create([
                'name' => $faker->name,
                'first_name' => $faker->firstName,
                'second_name' => $faker->lastName,
                'birth_date' => $faker->date('Y-m-d', '1990-12-31'),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'dni' => $dni,
                'email' => $faker->email,
                'password' => $code . $dni,
                'code' => $code,
            ]);
        }
    }
}
