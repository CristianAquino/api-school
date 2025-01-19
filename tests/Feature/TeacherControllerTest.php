<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TeacherControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_teachers(): void
    {
        $response = $this->getJson('/api/teachers');

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

    public function test_can_create_teacher(): void
    {
        $c = DB::table('teachers')->latest("code_teacher")->first()->code_teacher;
        $i = (int)substr($c, 2) + 1;
        $code = 'TE' . $i;

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
            "message" => "teacher " . $data["first_name"] . " " . $data["second_name"] . " " . $data["name"] . " has been added successfully with code $code"
        ];

        $response = $this->postJson('/api/teachers', $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseHas('users', ["email" => $data["email"]]);
    }

    public function test_can_show_teacher(): void
    {
        // random data
        $query = DB::table('teachers')->inRandomOrder()->first();
        $response = $this->getJson("/api/teachers/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'name',
                'first_name',
                'second_name',
                'phone',
                'birth_date',
                'address',
                'email',
                'dni',
                'code',
                'courses'
            ]
        );
    }

    public function test_assign_course_teacher(): void
    {
        $teacher = DB::table('teachers')->inRandomOrder()->first();
        $user = DB::table('users')->where('userable_id', $teacher->id)->first();
        $course = DB::table('courses')->inRandomOrder()->first();

        $message = [
            "message" => "Professor " . $user->first_name . " " . $user->second_name . " has been successfully assigned course $course->course"
        ];

        $response = $this->postJson("/api/teachers/$teacher->id/courses/$course->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_update_teacher(): void
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
        $teacher = DB::table('teachers')->inRandomOrder()->first();

        $message = [
            "message" => "The teacher with code $teacher->code_teacher has been successfully updated"
        ];

        $response = $this->putJson("/api/teachers/$teacher->id", $data);
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato existe en la base de datos
        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'code_teacher' => $teacher->code_teacher
        ]);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => $data["email"],
            'userable_id' => $teacher->id,
        ]);
    }
}
