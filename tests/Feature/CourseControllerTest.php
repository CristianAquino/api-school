<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\Course;
use App\Models\GradeLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    private const BASE_URL = '/api/courses';
    protected Course $course;
    protected int $course_total;
    protected GradeLevel $gradeLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            AuthenticateWithCookie::class,
            JWTMiddleware::class
        ]);

        $this->postJson('/api/login', [
            "code" => "AD20250000",
            "password" => "12345678"
        ]);

        // random data
        $query = Course::query();
        $this->course = $query->inRandomOrder()->first();
        $this->course_total = $query->count();
        $this->gradeLevel = GradeLevel::inRandomOrder()->first();
    }

    public function test_can_list_courses(): void
    {
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount("courses", $this->course_total);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'course',
                        'description'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_soft_list_coures(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->course->id
        );

        $response = $this->getJson(self::BASE_URL . "/soft_list");
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['course' => $this->course->course]);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'course',
                        'description'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_create_course(): void
    {
        $faker = Faker::create();
        $course = $faker->word();
        $grade = $this->gradeLevel->grade_id;
        $level = $this->gradeLevel->level_id;
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
        $this->assertDatabaseCount("courses", $this->course_total + 1);
    }

    public function test_can_create_course_with_schedule(): void
    {
        $faker = Faker::create();
        $course = $faker->word();
        $grade = $this->gradeLevel->grade_id;
        $level = $this->gradeLevel->level_id;
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
        $this->assertDatabaseCount("courses", $this->course_total + 1);
        $this->assertDatabaseHas('courses', [
            'course' => $data["course"],
        ]);
    }

    public function test_can_show_course(): void
    {
        $response = $this->getJson(self::BASE_URL . "/" . $this->course->id);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['course' => $this->course->course]);
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
        $data = [
            'course' => $course,
            'grade_level_id' => $this->gradeLevel->id
        ];
        $message = [
            "message" => "The course $course has been successfully updated"
        ];

        $response = $this->putJson(
            "api/courses/" . $this->course->id,
            $data
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseCount("courses", $this->course_total);
        $this->assertDatabaseHas('courses', [
            'course' => $data["course"],
        ]);
    }

    public function test_can_update_course_with_schedule(): void
    {
        $faker = Faker::create();
        $course = $faker->word();
        $day = strtolower($faker->dayOfWeek());
        $schedule = DB::table('schedules')->inRandomOrder()->first();
        $data = [
            'course' => $course,
            'grade_level_id' => $this->gradeLevel->id,
            'schedule_id' => $schedule->id,
            'day' => $day
        ];
        $message = [
            "message" => "The course $course has been successfully updated"
        ];

        $response = $this->putJson(
            "api/courses/" . $this->course->id,
            $data
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseCount("courses", $this->course_total);
        $this->assertDatabaseHas('courses', [
            'grade_level_id' => $data["grade_level_id"],
        ]);
    }

    public function test_can_soft_destroy_course(): void
    {
        $message = [
            "message" => "the course " . $this->course->course . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->course->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_course(): void
    {
        $message = [
            "message" => "the course " . $this->course->course . " has been successfully restored"
        ];
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->course->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->course->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_course(): void
    {
        $message = [
            "message" => "the course " . $this->course->course . " has been successfully deleted permanently"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->course->id
        );

        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->course->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("courses", $this->course_total - 1);
    }
}
