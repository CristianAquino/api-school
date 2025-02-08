<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\Student;
use App\Models\User;
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
    private const BASE_URL = '/api/students';
    protected Student $student;
    protected int $student_total;
    protected User $user;

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
        $query = Student::query();
        $this->student = $query->inRandomOrder()->first();
        $this->student_total = $query->count();
        $this->user = $this->student->user;
    }

    public function test_can_list_students(): void
    {
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount("students", $this->student_total);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'names',
                        'first_name',
                        'second_name',
                        'code'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_soft_list_students(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->student->id
        );

        $response = $this->getJson(self::BASE_URL . "/soft_list");
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'names',
                        'first_name',
                        'second_name',
                        'code'
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_me_student_information(): void
    {
        $auth = $this->postJson('/api/login', [
            "code" => $this->user->code,
            "password" => $this->user->code . $this->user->dni
        ]);
        $token = $auth["token"];

        $response = $this
            ->withHeader("authorization", "Bearer  $token")
            ->getJson(self::BASE_URL . "/me");
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'names',
                'first_name',
                'second_name',
                'phone',
                'birth_date',
                'address',
                'email',
                'dni',
                'code',
                'academic_year',
                'level',
                'grade',
                'courses'
            ]
        );
    }

    public function test_can_show_student(): void
    {
        $response = $this->getJson(self::BASE_URL . "/" . $this->student->id);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'names',
                'first_name',
                'second_name',
                'phone',
                'birth_date',
                'address',
                'email',
                'dni',
                'code',
                'academic_year',
                'level',
                'grade',
                'courses'
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
        $code = $this->user->code;

        $message = ["message" => "The student with code $code has been successfully updated"];

        $response = $this->putJson(
            self::BASE_URL . "/" . $this->student->id,
            $data
        );

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('students', [
            'id' => $this->student->id,
        ]);
        $this->assertDatabaseHas('users', [
            "email" => $data["email"],
            "code" => $code
        ]);
    }

    public function test_can_soft_destroy_student(): void
    {
        $message = [
            "message" => "the student " . $this->user->first_name . " " . $this->user->second_name . " " . $this->user->name . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->student->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_student(): void
    {
        $message = [
            "message" => "the student " . $this->user->first_name . " " . $this->user->second_name . " " . $this->user->name . " has been successfully restored"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->student->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->student->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_student(): void
    {
        $message = [
            "message" => "the student with code " . $this->user->code . " has been successfully deleted permanently"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->student->id
        );

        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->student->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("students", $this->student_total - 1);
    }
}
