<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    private const BASE_URL = '/api/teachers';
    protected Teacher $teacher;
    protected int $teacher_total;
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
        $query = Teacher::query();
        $this->teacher = $query->inRandomOrder()->first();
        $this->teacher_total = $query->count();
        $this->user = $this->teacher->user;
    }

    public function test_can_list_teachers(): void
    {
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount("teachers", $this->teacher_total);
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

    public function test_can_view_soft_list_teachers(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->teacher->id
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

    public function test_can_create_teacher(): void
    {
        $cod = User::where('userable_type', Teacher::class)
            ->latest("id")
            ->first()
            ->code;
        $i = (int)substr($cod, 2) + 1;
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
        ];
        $message = [
            "message" => "teacher " . $data["first_name"] . " " . $data["second_name"] . " " . $data["name"] . " has been added successfully with code $code"
        ];

        $response = $this->postJson(self::BASE_URL, $data);
        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseHas('users', ["email" => $data["email"]]);
        $this->assertDatabaseCount("teachers", $this->teacher_total + 1);
    }

    public function test_can_view_me_teacher_information(): void
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

    public function test_can_show_teacher(): void
    {
        $response = $this->getJson(
            self::BASE_URL . "/" . $this->teacher->id
        );
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
        $course = DB::table('courses')->inRandomOrder()->first();

        $message = [
            "message" => "Professor " . $this->user->first_name . " " . $this->user->second_name . " has been successfully assigned course $course->course"
        ];

        $response = $this->postJson(
            self::BASE_URL . "/" . $this->teacher->id . "/courses/$course->id"
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
        $this->assertDatabaseHas('courses', [
            "id" => $course->id,
            "teacher_id" => $this->teacher->id
        ]);
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
        $message = [
            "message" => "The teacher with code " . $this->user->code . " has been successfully updated"
        ];

        $response = $this->putJson(
            self::BASE_URL . "/" . $this->teacher->id,
            $data
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato existe en la base de datos
        $this->assertDatabaseHas('teachers', [
            'id' => $this->teacher->id,
        ]);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => $data["email"],
            'code' => $this->user->code,
            'userable_id' => $this->teacher->id,
        ]);
    }

    public function test_can_soft_destroy_teacher(): void
    {
        $message = [
            "message" => "the teacher with code " . $this->user->code . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->teacher->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_teacher(): void
    {
        $message = [
            "message" => "the teacher with code " . $this->user->code . " has been successfully restored"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->teacher->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->teacher->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_teacher(): void
    {
        $message = [
            "message" => "the teacher with code " . $this->user->code . " has been successfully deleted permanently"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->teacher->id
        );

        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->teacher->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("teachers", $this->teacher_total - 1);
    }
}
