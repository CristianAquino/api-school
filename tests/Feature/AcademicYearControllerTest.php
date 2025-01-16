<?php

namespace Tests\Feature;

use Database\Seeders\AcademicYearsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;


class AcademicYearControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_can_list_academic_years(): void
    {
        // seeder
        // $this->seed(AcademicYearsTableSeeder::class);
        $response = $this->getJson('/api/academic_years');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1);
        $response->assertJsonIsArray();
        $response->assertExactJsonStructure(
            [
                '*' => [
                    'id',
                    'year',
                    'start_date',
                    'end_date',
                ]
            ]
        );
    }

    public function test_can_create_academic_year(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];
        $message = [
            "message" => "The academic year $year has been successfully created"
        ];

        $response = $this->postJson('/api/academic_years', $data);

        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('academic_years', $data);
    }

    public function test_can_show_academic_year(): void
    {
        // random data
        $query = DB::table('academic_years')->inRandomOrder()->first();
        $response = $this->getJson("/api/academic_years/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'id',
                'year',
                'start_date',
                'end_date',
            ]
        );
    }

    public function test_can_show_last_academic_year(): void
    {
        $latestYear = date("Y");

        $response = $this->getJson('/api/academic_years/last_year');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath("year", $latestYear);
        $response->assertExactJsonStructure(
            [
                'id',
                'year',
                'start_date',
                'end_date',
            ]
        );
    }

    public function test_can_update_academic_year(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        // radom data
        $query = DB::table('academic_years')->inRandomOrder()->first();

        $message = [
            "message" => "the academic year $query->year has been successfully updated to $year"
        ];

        $response = $this->putJson("/api/academic_years/$query->id", $data);

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('academic_years', $data);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount("academic_years", 1);
    }

    public function test_can_delete_academic_year_with_relations(): void
    {
        // radom data
        $query = DB::table('academic_years')->inRandomOrder()->first();

        $message = [
            "message" => "the academic year $query->year cannot be deleted because it has enrollements"
        ];

        $response = $this->deleteJson("/api/academic_years/$query->id");

        // test response
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson($message);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount("academic_years", 1);
    }
}
