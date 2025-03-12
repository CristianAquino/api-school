<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Qualification;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class QualificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Qualification::truncate();

        $students = Student::all();
        $faker = Faker::create();
        $prom = (int)config('app.avg_note');

        foreach ($students as $student) {
            $year = AcademicYear::latest("year")->first();
            $lastEnrollement = $student->enrollements()->latest("academic_year_id")->first();
            if ($year->year == $lastEnrollement->academic_year->year) {
                $dl = $lastEnrollement->grade_level_id;
                $query = GradeLevel::find($dl);
                $courses = $query->courses;
                foreach ($courses as $course) {
                    for ($i = 0; $i < $prom; $i++) {
                        $letter_note = null;
                        $note = $faker->numberBetween(1, 20);
                        $avg = $note / $prom;
                        $correspondencias = [
                            'AD' => [18, 20],
                            'A' => [16, 17],
                            'B' => [10, 15],
                            'C' => [0, 9],
                        ];
                        foreach ($correspondencias as $letter => [$min, $max]) {
                            if ($note >= $min && $note <= $max) {
                                $letter_note = $letter;
                                break;
                            }
                        }

                        Qualification::create([
                            'student_id' => $student->id,
                            'number_note' => $note,
                            'letter_note' => $letter_note,
                            'avg' => $avg,
                            'course_id' => $course->id,
                        ]);
                    }
                }
            }
        }
    }
}
