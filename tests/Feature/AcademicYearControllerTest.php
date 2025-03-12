<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateWithCookie;
use App\Http\Middleware\JWTMiddleware;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Faker\Factory as Faker;

class AcademicYearControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private const BASE_URL = '/api/academic_years';
    protected AcademicYear $academic_year;
    protected int $academic_year_total;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            AuthenticateWithCookie::class,
            JWTMiddleware::class
        ]);

        $auth = $this->postJson('/api/login', [
            "code" => "AD20250000",
            "password" => "12345678"
        ]);

        // random data
        $query = AcademicYear::query();
        $this->academic_year = $query->inRandomOrder()->first();
        $this->academic_year_total = $query->count();
    }

    public function test_can_list_academic_years(): void
    {
        // seeder
        // $this->seed(AcademicYearsTableSeeder::class);
        $response = $this->getJson(self::BASE_URL);
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount(
            "academic_years",
            $this->academic_year_total
        );
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'year',
                        'start_date',
                        'end_date',
                    ]
                ],
                'pagination',
            ]
        );
    }

    public function test_can_view_soft_list_academic_years(): void
    {
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->academic_year->id
        );

        $response = $this->getJson(self::BASE_URL . "/soft_list");
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'year',
                        'start_date',
                        'end_date',
                    ]
                ],
                'pagination',
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

        $response = $this->postJson(self::BASE_URL, $data);
        // test response
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('academic_years', $data);
        $this->assertDatabaseCount(
            "academic_years",
            $this->academic_year_total + 1
        );
    }

    public function test_can_show_academic_year(): void
    {
        $response = $this->getJson(
            self::BASE_URL . "/" . $this->academic_year->id
        );
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

        $response = $this->getJson(self::BASE_URL . "/last_year");
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
        $message = [
            "message" => "the academic year " . $this->academic_year->year . " has been successfully updated to $year"
        ];

        $response = $this->putJson(
            self::BASE_URL . "/" . $this->academic_year->id,
            $data
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        // verifica si el dato ha sido guardado en la base de datos
        $this->assertDatabaseHas('academic_years', $data);
        // verifica si hay la misma cantidad de datos
        $this->assertDatabaseCount(
            "academic_years",
            $this->academic_year_total
        );
    }

    public function test_can_soft_destroy_academic_year(): void
    {
        $message = [
            "message" => "the academic year " . $this->academic_year->year . " has been successfully deleted"
        ];

        $response = $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->academic_year->id
        );
        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
    }

    public function test_can_restore_academic_year(): void
    {
        $message = [
            "message" => "the academic year " . $this->academic_year->year . " has been successfully restored"
        ];

        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->academic_year->id
        );

        $response = $this->postJson(
            self::BASE_URL . "/restore/" . $this->academic_year->id
        );
        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
    }

    public function test_can_destroy_academic_year(): void
    {
        $message = [
            "message" => "the academic year " . $this->academic_year->year . " has been successfully deleted permanently"
        ];
        $this->deleteJson(
            self::BASE_URL . "/soft_destroy/" . $this->academic_year->id
        );
        $response = $this->deleteJson(
            self::BASE_URL . "/destroy/" . $this->academic_year->id
        );

        // test response
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJson($message);
        $this->assertDatabaseCount(
            "academic_years",
            $this->academic_year_total - 1
        );
    }
}
