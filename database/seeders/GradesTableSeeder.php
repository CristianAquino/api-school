<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Level;
use Illuminate\Database\Seeder;

class GradesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        Grade::truncate();
        $grades = [
            ['grade' => '1'],
            ['grade' => '2'],
            ['grade' => '3'],
            ['grade' => '4'],
            ['grade' => '5'],
            ['grade' => '6'],
        ];
        $levels = Level::all();

        foreach ($levels as $level) {
            foreach ($grades as $grade) {
                if ($level->level == 'Secondary' && $grade['grade'] == '6') {
                    continue;
                } else {
                    Grade::create([
                        'level_id' => $level->id,
                        'grade' => $grade['grade'],
                    ]);
                }
            }
        }
    }
}
