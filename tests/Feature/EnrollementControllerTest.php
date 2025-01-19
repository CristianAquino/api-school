<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EnrollementControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_enrollements(): void
    {
        $response = $this->getJson('/api/enrollements');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'academic_year',
                    'grade',
                    'level',
                    'student'
                ]
            ]
        );
    }

    public function test_can_create_enrollement(): void
    {
        $academic_year = DB::table('academic_years')->latest("id")->first();
        $gradeLevel = DB::table('grade_level')->inRandomOrder()->first();

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
        $message = [
            "message" => "The registration for student " . $data["first_name"] . " " . $data["second_name"] . " " . $data["name"] . " has been created successfully"
        ];

        $response = $this->postJson("/api/enrollements/academic_years/$academic_year->id/grade_level/$gradeLevel->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseHas('users', ["email" => $data["email"]]);
    }

    public function test_can_show_enrollement(): void
    {
        // random data
        $query = DB::table('enrollements')->inRandomOrder()->first();
        $response = $this->getJson("/api/enrollements/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'academic_year',
                'grade',
                'level',
                'student'
            ]
        );
    }
}
