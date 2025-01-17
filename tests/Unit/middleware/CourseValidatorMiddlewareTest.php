<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\CourseValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CourseValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_missing_required_fields(): void
    {
        $query = DB::table("grade_level")->inRandomOrder()->first();
        $grade = $query->grade_id;
        $level = $query->level_id;

        $message = "The course field is required.";

        $request = Request::create(
            "/levels/$level/grades/$grade/courses",
            "POST",
            []
        );

        // middleware
        $middleware = new CourseValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertArrayHasKey("course", $errors);
        $this->assertStringContainsString($message, $errors["course"][0]);
    }

    public function test_invalid_day(): void
    {
        $faker = Faker::create();
        $query = DB::table("grade_level")->inRandomOrder()->first();
        $grade = $query->grade_id;
        $level = $query->level_id;

        $data = [
            "course" => $faker->word(),
            "day" => $faker->word(),
        ];

        $message = "The day must be a valid day of the week.";

        $request = Request::create(
            "/levels/$level/grades/$grade/courses",
            "POST",
            $data
        );

        $middleware = new CourseValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("day", $errors);
        $this->assertStringContainsString($message, $errors["day"][0]);
    }

    public function test_invalid_schedule_id(): void
    {
        $faker = Faker::create();
        $query = DB::table("grade_level")->inRandomOrder()->first();
        $grade = $query->grade_id;
        $level = $query->level_id;

        $data = [
            "course" => $faker->word(),
            "schedule_id" => $faker->numberBetween(100, 200)
        ];

        $message = "The selected schedule id is invalid.";

        $request = Request::create(
            "/levels/$level/grades/$grade/courses",
            "POST",
            $data
        );

        $middleware = new CourseValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("schedule_id", $errors);
        $this->assertStringContainsString($message, $errors["schedule_id"][0]);
    }

    public function test_invalid_grade_level_id(): void
    {
        $faker = Faker::create();

        $data = [
            "course" => $faker->word(),
            "grade_level_id" => $faker->numberBetween(100, 200)
        ];

        $message = "The selected grade level id is invalid.";

        $request = Request::create(
            "/courses/{course}",
            "PUT",
            $data
        );

        $middleware = new CourseValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("grade_level_id", $errors);
        $this->assertStringContainsString($message, $errors["grade_level_id"][0]);
    }
}
