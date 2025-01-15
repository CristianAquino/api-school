<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\AcademicYearValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as Faker;

class AcademicYearValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_invalid_start_date_format(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '-01-01',
            'end_date' => $year . '/12/31',
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $message = "The start date " . $request["start_date"] . " is not in Y/m/d format or is an invalid date";

        $middleware = new AcademicYearValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("start_date", $errors);
        $this->assertStringContainsString($message, $errors["start_date"][0]);
    }

    public function test_start_date_year_mismatch(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => ($year - 1) . '/01/01',
            'end_date' => $year . '/12/31',
        ];
        $message = "The start date must start in the year " . $year;

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new AcademicYearValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("start_date", $errors);
        $this->assertStringContainsString($message, $errors["start_date"][0]);
    }

    public function test_end_date_before_year(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => ($year - 1) . '/12/31',
        ];
        $message = "The end date must end int the year $year or a later year";

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new AcademicYearValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("end_date", $errors);
        $this->assertStringContainsString($message, $errors["end_date"][0]);
    }

    public function test_missing_required_fields(): void
    {
        $message = [
            "year" => "The year field is required.",
            "start_date" => "The start date field is required.",
            "end_date" => "The end date field is required.",
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            []
        );

        // middleware
        $middleware = new AcademicYearValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertStringContainsString($message["year"], $errors["year"][0]);
        $this->assertStringContainsString($message["start_date"], $errors["start_date"][0]);
        $this->assertStringContainsString($message["end_date"], $errors["end_date"][0]);
    }

    public function test_successful_validation(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new AcademicYearValidatorMiddleware();
        $response = $middleware->handle($request, function () {
            // Return value must be of type Symfony\Component\HttpFoundation\Response
            return response()->json(['success' => true]);
        });

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
