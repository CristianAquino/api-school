<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ScheduleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    private const BASE_URL = '/api/schedules';
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
        $query = DB::table('schedules');
        $this->random = $query->inRandomOrder()->first();
        $this->counter = $query->count();
    }

    public function test_can_list_schedules(): void
    {
        $response = $this->getJson(self::BASE_URL);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'start_time',
                    'end_time',
                ]
            ]

        );
    }

    public function test_can_view_soft_list_schedules(): void
    {
        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);

        $response = $this
            ->getJson(self::BASE_URL . "/soft_list");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['start_time' => $this->random->start_time]);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'start_time',
                        'end_time',
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_create_schedule(): void
    {
        $data = [
            'start_time' => "14:00",
            'end_time' => "16:00",
        ];
        $message = [
            "message" => "Schedule created successfully"
        ];

        $response = $this->postJson(self::BASE_URL, $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('schedules', $data);
        $this->assertDatabaseCount("schedules", $this->counter + 1);
    }

    public function test_can_show_schedule(): void
    {
        // random data
        $response = $this->getJson(self::BASE_URL . "/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'start_time',
                'end_time',
                'courses'
            ]
        );
    }

    public function test_can_update_schedule(): void
    {
        $data = [
            'start_time' => "14:00",
            'end_time' => "16:00",
        ];

        $message = [
            "message" => "Schedule has been successfully updated"
        ];

        $response = $this->putJson(self::BASE_URL . "/" . $this->random->id, $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('schedules', [
            "start_time" => $data["start_time"]
        ]);
        $this->assertDatabaseCount("schedules", $this->counter);
    }

    public function test_can_soft_destroy_schedule(): void
    {
        $message = [
            "message" => "the schedule has been successfully deleted"
        ];
        $response = $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_schedule(): void
    {
        $message = [
            "message" => "the schedule has been successfully restored"
        ];
        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->postJson(self::BASE_URL . "/restore/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_schedule(): void
    {
        $message = [
            "message" => "the scheduler has been successfully deleted permanently"
        ];

        $this->deleteJson(self::BASE_URL . "/soft_destroy/" . $this->random->id);
        $response = $this->deleteJson(self::BASE_URL . "/destroy/" . $this->random->id);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount("schedules", $this->counter - 1);
    }
}
