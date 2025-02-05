<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;

class LevelControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    private const BASE_URL = '/api/levels';
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

        $query = DB::table('levels');
        // random data
        $this->random = $query->inRandomOrder()->first();
        $this->counter = $query->count();
    }

    public function test_can_list_levels(): void
    {
        $response = $this->get(self::BASE_URL);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['level' => $this->random->level]);
        $this->assertDatabaseCount("levels", $this->counter);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'level',
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
        $response->assertJsonFragment(['level' => $this->random->level]);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'level',
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_create_level(): void
    {
        $faker = Faker::create();
        $level = $faker->word();
        $data = [
            'level' => $level,
        ];
        $message = [
            "message" => "The level $level has been successfully created"
        ];

        $response = $this->postJson(self::BASE_URL, $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('levels', $data);
        $this->assertDatabaseCount("levels", $this->counter + 1);
    }

    public function test_can_show_level(): void
    {
        $response = $this->getJson(self::BASE_URL . "/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['level' => $this->random->level]);
        $response->assertExactJsonStructure(
            [
                'id',
                'level',
                'grades',
            ]
        );
    }

    public function test_can_update_level(): void
    {
        $faker = Faker::create();
        $level = $faker->word();
        $data = [
            'level' => $level,
        ];

        $message = [
            "message" => "The level " . $this->random->level . " has been successfully updated to $level"
        ];

        $response = $this->putJson(self::BASE_URL . "/" . $this->random->id, $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('levels', $data);
        $this->assertDatabaseCount("levels", $this->counter);
    }

    public function test_can_soft_destroy_level(): void
    {
        $message = [
            "message" => "The level " . $this->random->level . " has been successfully deleted"
        ];
        $response = $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_level(): void
    {
        $message = [
            "message" => "the level " . $this->random->level . " has been successfully restored"
        ];
        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->postJson(self::BASE_URL . "/restore/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_academic_year(): void
    {
        $message = [
            "message" => "the level " . $this->random->level . " has been successfully deleted permanently"
        ];

        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->deleteJson(self::BASE_URL . "/destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("levels", $this->counter - 1);
    }
}
