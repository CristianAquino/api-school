<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StudentControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_students(): void
    {
        $response = $this->getJson('/api/students');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'names',
                    'first_name',
                    'second_name',
                ]
            ]
        );
    }

    public function test_can_show_student(): void
    {
        // random data
        $query = DB::table('students')->inRandomOrder()->first();
        $response = $this->getJson("/api/students/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'names',
                'first_name',
                'second_name',
                'academic_year',
                'level',
                'grade',
                'code_student',
                'course'
            ]
        );
    }

    public function test_can_update_student(): void
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
        ];

        // radom data
        $student = DB::table('students')->inRandomOrder()->first();

        $message = ["message" => "The student with code $student->code_student has been successfully updated"];

        $response = $this->putJson("/api/students/$student->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('students', [
            "code_student" => $student->code_student
        ]);
        $this->assertDatabaseHas('users', [
            "email" => $data["email"]
        ]);
    }
}
