<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\GradeLevel;
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
        GradeLevel::truncate();
        Grade::truncate();

        $grades = ['1', '2', '3', '4', '5', '6',];
        $levels = Level::all();

        foreach ($levels as $level) {
            foreach ($grades as $grade) {
                $exists = Grade::where('grade', $grade)->exists();
                if (!$exists) {
                    $g = Grade::create([
                        'grade' => $grade,
                    ]);

                    $level->grades()->attach($g);
                } else {
                    if ($level->level == "Secondary" && $grade == "6") {
                        continue;
                    }
                    $g = Grade::where('grade', $grade)->first();
                    $level->grades()->attach($g);
                }
            }
        }
    }
}