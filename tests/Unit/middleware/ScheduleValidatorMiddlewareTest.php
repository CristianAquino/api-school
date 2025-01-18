<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\ScheduleValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as Faker;

class ScheduleValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_missing_required_fields(): void
    {
        $message = [
            "start_time" => "The start time field is required.",
            "end_time" => "The end time field is required.",
        ];

        $request = Request::create(
            '/api/schedules',
            'POST',
            []
        );

        // middleware
        $middleware = new ScheduleValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertStringContainsString($message["start_time"], $errors["start_time"][0]);
        $this->assertStringContainsString($message["end_time"], $errors["end_time"][0]);
    }

    public function test_invalid_time_format(): void
    {
        $faker = Faker::create();
        $data = [
            'start_time' => $faker->date(now()),
            'end_time' => $faker->date(now()->addHours(2))
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $message = [
            "start_time" => "The start time field must match the format H:i.",
            "end_time" => "The end time field must match the format H:i."
        ];

        $middleware = new ScheduleValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("start_time", $errors);
        $this->assertStringContainsString($message["start_time"], $errors["start_time"][0]);
        $this->assertArrayHasKey("end_time", $errors);
        $this->assertStringContainsString($message["end_time"], $errors["end_time"][0]);
    }

    public function test_invalid_end_time_mismatch(): void
    {
        $faker = Faker::create();
        $time = $faker->date('H:i', 'now');
        $data = [
            'start_time' => $time,
            'end_time' => $time
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $message = "The end time must be after the start time.";

        $middleware = new ScheduleValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("end_time", $errors);
        $this->assertStringContainsString($message, $errors["end_time"][0]);
    }

    public function test_successful_validation(): void
    {
        $faker = Faker::create();
        $data = [
            'start_time' => $faker->date('H:i', now()),
            'end_time' => $faker->date('H:i', now()->addHours(2))
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new ScheduleValidatorMiddleware();
        $response = $middleware->handle($request, function () {
            // Return value must be of type Symfony\Component\HttpFoundation\Response
            return response()->json(['success' => true]);
        });

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
