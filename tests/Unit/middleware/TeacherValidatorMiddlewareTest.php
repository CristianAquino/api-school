<?php

namespace Tests\Unit\middleware;

use App\Http\Middleware\UserValidatorMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TeacherValidatorMiddlewareTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_missing_required_fields(): void
    {
        $message = [
            "name" => "The name field is required.",
            "first_name" => "The first name field is required.",
            "second_name" => "The second name field is required.",
            "phone" => "The phone field is required.",
            "address" => "The address field is required.",
            "dni" => "The dni field is required.",
            "email" => "The email field is required.",
        ];

        $request = Request::create(
            '/api/teachers',
            'POST',
            []
        );

        // middleware
        $middleware = new UserValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertStringContainsString($message["name"], $errors["name"][0]);
        $this->assertStringContainsString($message["first_name"], $errors["first_name"][0]);
        $this->assertStringContainsString($message["second_name"], $errors["second_name"][0]);
        $this->assertStringContainsString($message["phone"], $errors["phone"][0]);
        $this->assertStringContainsString($message["address"], $errors["address"][0]);
        $this->assertStringContainsString($message["dni"], $errors["dni"][0]);
        $this->assertStringContainsString($message["email"], $errors["email"][0]);
    }

    public function test_repeat_dni(): void
    {
        $user = DB::table('users')->inRandomOrder()->first();
        $faker = Faker::create();
        $data = [
            'name' => $faker->name,
            'first_name' => $faker->firstName,
            'second_name' => $faker->lastName,
            'birth_date' => $faker->date('Y-m-d', '1990-12-31'),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
            'dni' => $user->dni,
            'email' => $faker->email,
            'password' => $faker->paragraph(1),
        ];

        $message = [
            "dni" => "The dni has already been taken.",
        ];

        $request = Request::create(
            '/api/teachers',
            'POST',
            $data
        );

        // middleware
        $middleware = new UserValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertStringContainsString($message["dni"], $errors["dni"][0]);
    }

    public function test_repeat_email(): void
    {
        $user = DB::table('users')->inRandomOrder()->first();
        $faker = Faker::create();
        $data = [
            'name' => $faker->name,
            'first_name' => $faker->firstName,
            'second_name' => $faker->lastName,
            'birth_date' => $faker->date('Y-m-d', '1990-12-31'),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
            'dni' => (string)$faker->randomNumber(8, true),
            'email' => $user->dni,
            'password' => $faker->paragraph(1),
        ];

        $message = [
            "email" => "The email field must be a valid email address.",
        ];

        $request = Request::create(
            '/api/teachers',
            'POST',
            $data
        );

        // middleware
        $middleware = new UserValidatorMiddleware();
        $response = $middleware->handle($request, function () {});
        // get errors
        $errors = json_decode($response->getContent(), true);

        // test code response
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        // test message errors
        $this->assertStringContainsString($message["email"], $errors["email"][0]);
    }

    public function test_successful_validation(): void
    {
        $faker = Faker::create();
        $data = [
            'name' => $faker->name,
            'first_name' => $faker->firstName,
            'second_name' => $faker->lastName,
            'birth_date' => $faker->date('Y-m-d', '1990-12-31'),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
            'dni' => (string)$faker->randomNumber(8, true),
            'email' => $faker->email(),
            'password' => $faker->paragraph(1),
        ];

        $request = Request::create(
            '/api/academic_years',
            'POST',
            $data
        );

        $middleware = new UserValidatorMiddleware();
        $response = $middleware->handle($request, function () {
            // Return value must be of type Symfony\Component\HttpFoundation\Response
            return response()->json(['success' => true]);
        });

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
