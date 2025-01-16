<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\LevelValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LevelValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_missing_required_fields(): void
    {
        $message = "The level field is required.";

        $request = Request::create(
            '/api/levels',
            'POST',
            []
        );

        $middleware = new LevelValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("level", $errors);
        $this->assertStringContainsString($message, $errors["level"][0]);
    }

    public function test_repeat_level(): void
    {
        // DB::insert('insert into levels (level) values (?)', ["Primary"]);
        $data = [
            'level' => 'Primary',
        ];
        $message = "The level " . $data["level"] . " already exists.";

        $request = Request::create(
            '/api/levels',
            'POST',
            $data
        );

        $middleware = new LevelValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("level", $errors);
        $this->assertStringContainsString($message, $errors["level"][0]);
    }

    public function test_invalid_type_data_level(): void
    {
        $faker = Faker::create();
        $data = [
            'level' => $faker->numberBetween(1, 5),
        ];
        $message = "The level field must be a string.";

        $request = Request::create(
            '/api/levels',
            'POST',
            $data
        );

        $middleware = new LevelValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        $errors = json_decode($response->getContent(), true);

        // test response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertArrayHasKey("level", $errors);
        $this->assertStringContainsString($message, $errors["level"][0]);
    }
}
