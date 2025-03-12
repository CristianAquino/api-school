<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Level::truncate();

        $levels = ['Primary', 'Secondary',];

        foreach ($levels as $level) {
            Level::create(
                ['level' => $level]
            );
        }
    }
}