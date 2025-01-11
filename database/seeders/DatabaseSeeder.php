<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Schema::disableForeignKeyConstraints();
        $this->call([
            AdminsTableSeeder::class,
            AcademicYearsTableSeeder::class,
            LevelsTableSeeder::class,
            GradesTableSeeder::class,
            CoursesTableSeeder::class,
            SchedulesTableSeeder::class,
            CourseScheduleTableSeeder::class,
            TeachersTableSeeder::class,
            TeachersCoursesTableSeeder::class,
            EnrollementsTableSeeder::class,
            QualificationsTableSeeder::class
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
