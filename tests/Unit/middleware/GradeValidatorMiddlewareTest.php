<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\GradeValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as Faker;
use Tests\TestCase;

class GradeValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_missing_required_fields(): void
    {
        $message = "The grade field is required.";

        $level = DB::table('levels')->inRandomOrder()->first();

        $request = Request::create(
            "/api/levels/$level->id/grades",
            "POST",
            []
        );

        $middleware = new GradeValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("grade", $errors);
        $this->assertStringContainsString($message, $errors["grade"][0]);
    }

    public function test_invalid_type_data_level(): void
    {
        $faker = Faker::create();
        $data = [
            'grade' => $faker->numberBetween(1, 5),
        ];
        $message = "The grade field must be a string.";

        $level = DB::table('levels')->inRandomOrder()->first();

        $request = Request::create(
            "/api/levels/$level->id/grades",
            "POST",
            $data
        );

        $middleware = new GradeValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("grade", $errors);
        $this->assertStringContainsString($message, $errors["grade"][0]);
    }

    public function test_successful_validation(): void
    {
        $faker = Faker::create();
        $data = [
            'grade' => $faker->word(),
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new GradeValidatorMiddleware();
        $response = $middleware->handle($request, function () {
            // Return value must be of type Symfony\Component\HttpFoundation\Response
            return response()->json(['success' => true]);
        });

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
