<?php

namespace Tests\Feature;

use Database\Seeders\LevelsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_can_list_levels(): void
    {
        // seeder
        $this->seed(LevelsTableSeeder::class);

        $response = $this->get('/api/levels');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'level',
                ]
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

        $response = $this->postJson('/api/levels', $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('levels', $data);
    }

    public function test_can_show_level(): void
    {
        // seeder
        $this->seed(LevelsTableSeeder::class);

        $query = DB::table('levels')->inRandomOrder()->first();
        $response = $this->getJson("/api/levels/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
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
        // seeder
        $this->seed(LevelsTableSeeder::class);

        $faker = Faker::create();
        $level = $faker->word();
        $data = [
            'level' => $level,
        ];

        // radom data
        $query = DB::table('levels')->inRandomOrder()->first();
        $lev = $query->level;

        $message = [
            "message" => "The level $lev has been successfully updated to $level"
        ];

        $response = $this->putJson("/api/levels/$query->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('levels', $data);
    }

    public function test_can_delete_level(): void
    {
        // seeder
        $this->seed(LevelsTableSeeder::class);

        // radom data
        $query = DB::table('levels')->inRandomOrder()->first();

        $message = [
            "message" => "The level $query->level has been successfully deleted"
        ];

        $response = $this->deleteJson("/api/levels/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount("levels", 1);
    }
}
