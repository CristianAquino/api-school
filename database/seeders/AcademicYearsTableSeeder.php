<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // vaciamos la tabla
        AcademicYear::truncate();
        AcademicYear::create([
            'year' => date("Y"),
            'start_date' => date("Y/m/d"),
            'end_date' => date("Y/12/31"),
        ]);
    }
}
