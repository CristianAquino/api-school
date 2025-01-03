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
        $levels = [
            ['level' => 'Primary'],
            ['level' => 'Secondary'],
        ];
        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
