<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AdminControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    private const BASE_URL = '/api/admins';
    protected Admin $admin;
    protected int $admin_total;
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
        $query = Admin::query();
        $this->admin = $query->inRandomOrder()->first();
        $this->admin_total = $query->count();
        $this->user = $this->admin->user;
    }

    public function test_can_list_admins(): void
    {
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount("admins", $this->admin_total);
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

    public function test_can_view_soft_list_admins(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->admin->id
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

    public function test_can_create_admin(): void
    {
        $cod = User::where('userable_type', Admin::class)
            ->latest("id")
            ->first()
            ->code;
        $i = (int)substr($cod, 2) + 1;
        $code = 'AD' . $i;

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
            "message" => "admin " . $data["first_name"] . " " . $data["second_name"] . " " . $data["name"] . " has been added successfully with code $code"
        ];

        $response = $this->postJson(self::BASE_URL, $data);
        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        $this->assertDatabaseHas('users', ["email" => $data["email"]]);
        $this->assertDatabaseCount("admins", $this->admin_total + 1);
    }

    public function test_can_view_me_admin_information(): void
    {
        $response = $this->getJson(self::BASE_URL . "/me");
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
            ]
        );
    }

    public function test_can_show_admin(): void
    {
        $response = $this->getJson(
            self::BASE_URL . "/" . $this->admin->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'names',
                'first_name',
                'second_name',
                'code'
            ]
        );
    }

    public function test_can_update_admin(): void
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
            "message" => "The admin with code " . $this->user->code . " has been successfully updated"
        ];

        $response = $this
            ->putJson(
                self::BASE_URL . "/" . $this->admin->id,
                $data
            );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato existe en la base de datos
        $this->assertDatabaseHas('admins', [
            'id' => $this->admin->id,
        ]);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => $data["email"],
            'code' => $this->user->code,
            'userable_id' => $this->admin->id,
        ]);
    }

    public function test_can_soft_destroy_admin(): void
    {
        $message = [
            "message" => "the admin with code " . $this->user->code . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->admin->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_admin(): void
    {
        $message = [
            "message" => "the admin with code " . $this->user->code . " has been successfully restored"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->admin->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->admin->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_admin(): void
    {
        $message = [
            "message" => "the admin with code " . $this->user->code . " has been successfully deleted permanently"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->admin->id
        );

        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->admin->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("admins", $this->admin_total - 1);
    }
}
