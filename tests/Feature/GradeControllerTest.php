<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    private const BASE_URL = '/api/grades';
    protected $random;
    protected int $counter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([AuthenticateWithCookie::class, JWTMiddleware::class]);

        $this->postJson('/api/login', [
            "code" => "AD20250000",
            "password" => "12345678"
        ]);

        // random data
        $query = DB::table('grades');
        $this->random = $query->inRandomOrder()->first();
        $this->counter = $query->count();
    }

    public function test_can_list_grades(): void
    {
        $response = $this->get(self::BASE_URL);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['grade' => $this->random->grade]);
        $this->assertDatabaseCount("grades", $this->counter);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'grade',
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_soft_list_levels(): void
    {
        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);

        $response = $this
            ->getJson(self::BASE_URL . "/soft_list");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['grade' => $this->random->grade]);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'grade',
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_cannot_create_repeat_grade(): void
    {
        $faker = Faker::create();
        $grade = (string)$faker->numberBetween(1, 5);
        $level = DB::table('levels')->inRandomOrder()->first();
        $data = [
            'grade' => $grade,
        ];

        $message = [
            "message" => "Grade $grade for level $level->level already exists, please enter another grade or modify the existing grade"
        ];

        $response = $this->postJson("api/levels/$level->id/grades", $data);

        // test response
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson($message);
        $this->assertDatabaseCount("grades", $this->counter);
    }

    public function test_can_create_grade_with_other_level(): void
    {
        $grade = "6";
        $level = DB::table('levels')->where("level", "Secondary")->first();
        $data = [
            'grade' => $grade,
        ];

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
        $this->assertDatabaseHas('grades', $data);
        $this->assertDatabaseCount("grades", $this->counter + 1);
    }

    public function test_can_show_grade(): void
    {
        $gradeLevel = DB::table('grade_level')->where('grade_id', $this->random->id)->inRandomOrder()->first();

        $response = $this->getJson("/api/levels/$gradeLevel->level_id/grades/$gradeLevel->grade_id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['grade' => $this->random->grade]);
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
        $gra = $this->random->grade;

        $message = [
            "message" => "The grade $gra has been successfully updated to $grade"
        ];

        $response = $this->putJson(self::BASE_URL . "/" . $this->random->id, $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('grades', $data);
        $this->assertDatabaseCount("grades", $this->counter);
    }

    public function test_can_soft_destroy_grade(): void
    {
        $message = [
            "message" => "The grade " . $this->random->grade . " has been successfully deleted"
        ];
        $response = $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_grade(): void
    {
        $message = [
            "message" => "the grade " . $this->random->grade . " has been successfully restored"
        ];
        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->postJson(self::BASE_URL . "/restore/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_grade(): void
    {
        $message = [
            "message" => "the grade " . $this->random->grade . " has been successfully deleted permanently"
        ];

        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->deleteJson(self::BASE_URL . "/destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("grades", $this->counter - 1);
    }
}
