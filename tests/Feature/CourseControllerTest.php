<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CourseControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_courses(): void
    {
        $response = $this->getJson('/api/courses');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'course',
                    'description'
                ]
            ]
        );
    }

    public function test_can_create_course(): void
    {
        $faker = Faker::create();
        $course = $faker->word();

        $query = DB::table('grade_level')->inRandomOrder()->first();
        $grade = $query->grade_id;
        $level = $query->level_id;

        $data = [
            'course' => $course,
        ];
        $message = [
            "message" => "The course $course has been successfully created"
        ];

        $response = $this->postJson(
            "api/levels/$level/grades/$grade/courses",
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('courses', $data);
    }

    public function test_can_create_with_schedule(): void
    {
        $faker = Faker::create();
        $course = $faker->word();

        $query = DB::table('grade_level')->inRandomOrder()->first();
        $grade = $query->grade_id;
        $level = $query->level_id;
        $schedule = DB::table('schedules')->inRandomOrder()->first();

        $data = [
            'course' => $course,
            'schedule_id' => $schedule->id,
            'day' => strtolower($faker->dayOfWeek()),
        ];
        $message = [
            "message" => "The course $course has been successfully created"
        ];

        $response = $this->postJson(
            "api/levels/$level/grades/$grade/courses",
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
    }

    public function test_can_show_course(): void
    {
        // random data
        $query = DB::table('courses')->inRandomOrder()->first();
        $response = $this->getJson("api/courses/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'course',
                'description',
                'level',
                'grade',
                'teacher',
                'schedule'
            ]
        );
    }

    public function test_can_update_course(): void
    {
        $faker = Faker::create();
        $course = $faker->word();

        $gradeLevel = DB::table('grade_level')->inRandomOrder()->first();
        $cur = DB::table('courses')->inRandomOrder()->first();

        $data = [
            'course' => $course,
            'grade_level_id' => $gradeLevel->id
        ];
        $message = [
            "message" => "The course $course has been successfully updated"
        ];

        $response = $this->putJson(
            "api/courses/$cur->id",
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_update_course_with_schedule(): void
    {
        $faker = Faker::create();
        $course = $faker->word();
        $day = strtolower($faker->dayOfWeek());

        $gradeLevel = DB::table('grade_level')->inRandomOrder()->first();
        $schedule = DB::table('schedules')->inRandomOrder()->first();
        $cur = DB::table('courses')->inRandomOrder()->first();

        $data = [
            'course' => $course,
            'grade_level_id' => $gradeLevel->id,
            'schedule_id' => $schedule->id,
            'day' => $day
        ];
        $message = [
            "message" => "The course $course has been successfully updated"
        ];

        $response = $this->putJson(
            "api/courses/$cur->id",
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }
}
