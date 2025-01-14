<?php

namespace Tests\Feature;

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

    public function test_can_list_academic_years()
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        $this->postJson('/api/academic_years', $data);

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

        $response = $this->postJson('/api/academic_years', $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(["message" => "The academic year $year has been successfully created"]);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('academic_years', $data);
    }

    public function test_can_show_academic_year(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        $this->postJson('/api/academic_years', $data);
        $query = DB::table('academic_years')->latest("id")->first();
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
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        $this->postJson('/api/academic_years', $data);
        $response = $this->getJson('/api/academic_years/last_year');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath("year", $year);
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

        $this->postJson('/api/academic_years', $data);
        $query = DB::table('academic_years')->latest("id")->first();

        $response = $this->putJson("/api/academic_years/$query->id", [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => ($year + 1) . '/12/31'
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson(["message" => "the academic year $year has been successfully updated"]);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount("academic_years", 1);
    }

    public function test_can_delete_academic_year(): void
    {
        $faker = Faker::create();
        $year = $faker->year();
        $data = [
            'year' => $year,
            'start_date' => $year . '/01/01',
            'end_date' => $year . '/12/31',
        ];

        $this->postJson('/api/academic_years', $data);
        $query = DB::table('academic_years')->latest("id")->first();

        $response = $this->deleteJson("/api/academic_years/$query->id");

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson(["message" => "the academic year $year has been successfully deleted"]);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount("academic_years", 0);
    }
}
