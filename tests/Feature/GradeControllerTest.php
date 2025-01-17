<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class GradeControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_grades(): void
    {
        $response = $this->get('/api/grades');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'grade',
                ]
            ]
        );
    }

    public function test_cannot_create_repeat_grade(): void
    {
        $faker = Faker::create();
        $grade = (string)$faker->numberBetween(1, 5);
        $data = [
            'grade' => $grade,
        ];
        $level = DB::table('levels')->inRandomOrder()->first();

        $message = [
            "message" => "Grade $grade for level $level->level already exists, please enter another grade or modify the existing grade"
        ];

        $response = $this->postJson("/api/levels/$level->id/grades", $data);

        // test response
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson($message);
    }

    public function test_can_create_grade_with_other_level(): void
    {
        $grade = "6";
        $data = [
            'grade' => $grade,
        ];

        $level = DB::table('levels')->where("level", "Secondary")->first();

        $message = [
            "message" => "Grade $grade has been correctly assigned to level $level->level"
        ];

        $response = $this->postJson("/api/levels/$level->id/grades", $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
    }

    public function test_can_create_grade(): void
    {
        $faker = Faker::create();
        $grade = $faker->word();
        $data = [
            'grade' => $grade,
        ];
        $level = DB::table('levels')->inRandomOrder()->first();

        $message = [
            "message" => "Grade $grade has been successfully created"
        ];

        $response = $this->postJson("/api/levels/$level->id/grades", $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
    }

    public function test_can_show_grade(): void
    {
        $gradeLevel = DB::table('grade_level')->inRandomOrder()->first();

        $response = $this->getJson("/api/levels/$gradeLevel->level_id/grades/$gradeLevel->grade_id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'grade',
                'level',
                'courses'
            ]
        );
    }

    public function test_can_update_grade(): void
    {
        $faker = Faker::create();
        $grade = $faker->word();
        $data = [
            'grade' => $grade,
        ];

        // radom data
        $query = DB::table('grades')->inRandomOrder()->first();
        $gra = $query->grade;

        $message = [
            "message" => "The grade $gra has been successfully updated to $grade"
        ];

        $response = $this->putJson("/api/grades/$query->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('grades', $data);
    }
}
