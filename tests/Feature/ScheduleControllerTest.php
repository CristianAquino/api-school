<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ScheduleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_schedules(): void
    {
        $response = $this->getJson('/api/schedules');

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

    public function test_can_create_schedule(): void
    {
        $faker = Faker::create();
        $data = [
            'start_time' => $faker->date('H:i', now()),
            'end_time' => $faker->date('H:i', now()->addHours(2)),
        ];
        $message = [
            "message" => "Schedule created successfully"
        ];

        $response = $this->postJson('/api/schedules', $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('schedules', $data);
    }

    public function test_can_show_schedule(): void
    {
        // random data
        $query = DB::table('schedules')->inRandomOrder()->first();
        $response = $this->getJson("/api/schedules/$query->id");

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
        $faker = Faker::create();
        $data = [
            'start_time' => $faker->date('H:i', now()),
            'end_time' => $faker->date('H:i', now()->addHours(2)),
        ];

        // radom data
        $query = DB::table('schedules')->inRandomOrder()->first();

        $message = [
            "message" => "Schedule has been successfully updated"
        ];

        $response = $this->putJson("/api/schedules/$query->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('schedules', $data);
    }
}
