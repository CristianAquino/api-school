<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\AcademicYear;
use App\Models\Enrollement;
use App\Models\GradeLevel;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;

class EnrollementControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    private const BASE_URL = '/api/enrollements';
    protected Enrollement $enrollement;
    protected int $enrollement_total;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            AuthenticateWithCookie::class,
            JWTMiddleware::class
        ]);

        $this->postJson('/api/login', [
            "code" => "AD20250000",
            "password" => "12345678"
        ]);

        // random data
        $query = Enrollement::query();
        $this->enrollement = $query->inRandomOrder()->first();
        $this->enrollement_total = $query->count();
    }

    public function test_can_list_enrollements(): void
    {
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount("enrollements", $this->enrollement_total);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'academic_year',
                        'grade',
                        'level',
                        'student'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_soft_list_enrollements(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->enrollement->id
        );

        $response = $this->getJson(self::BASE_URL . "/soft_list");
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $this->enrollement->id]);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'academic_year',
                        'grade',
                        'level',
                        'student'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_create_new_enrollement(): void
    {
        $academic_year = AcademicYear::latest("id")->first();
        $gradeLevel = GradeLevel::inRandomOrder()->first();

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

        $response = $this->postJson(
            self::BASE_URL . "/academic_years/$academic_year->id/grade_level/$gradeLevel->id",
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseCount("enrollements", $this->enrollement_total + 1);
        $this->assertDatabaseHas('users', ["email" => $data["email"]]);
    }

    public function test_can_create_current_enrollement(): void
    {
        $gradeLevel = GradeLevel::inRandomOrder()->first();
        $student = Student::inRandomOrder()->first();
        $message = [
            "message" => "The registration for student " . $student->user->first_name . " " . $student->user->second_name . " " . $student->user->name . " has been created successfully"
        ];

        $this->postJson('/api/academic_years', [
            'year' => '2026',
            'start_date' => '2026/01/01',
            'end_date' => '2026/12/31',
        ]);

        $response = $this->postJson(
            self::BASE_URL . "/academic_years/2/grade_level/$gradeLevel->id/student/$student->id"
        );
        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseCount("enrollements", $this->enrollement_total + 1);
    }

    public function test_can_show_enrollement(): void
    {
        $response = $this->getJson(
            self::BASE_URL . "/" . $this->enrollement->id
        );
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
                'code',
                'courses'
            ]
        );
    }

    public function test_can_soft_destroy_enrollement(): void
    {
        $message = [
            "message" => "the enrollement with code " . $this->enrollement->id . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->enrollement->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_enrollement(): void
    {
        $message = [
            "message" => "the enrollement with code " . $this->enrollement->id . " has been successfully restored"
        ];
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->enrollement->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->enrollement->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_enrollement(): void
    {
        $message = [
            "message" => "the enrollement with code " . $this->enrollement->id . " has been successfully deleted permanently"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->enrollement->id
        );

        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->enrollement->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("enrollements", $this->enrollement_total - 1);
    }
}
