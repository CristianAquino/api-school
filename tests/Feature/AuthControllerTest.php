<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private const API_TOKEN_COOKIE_NAME = 'api_token';

    public function test_user_can_login(): void
    {
        $data = [
            'code' => "AD20250000",
            'password' => "12345678"
        ];

        $response = $this->postJson('/api/login', $data);

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertCookie(self::API_TOKEN_COOKIE_NAME);
        $response->assertExactJsonStructure(
            [
                'token',
                'user',
            ]
        );
        // Verificar que el token no sea nulo
        $response->assertCookieNotExpired(self::API_TOKEN_COOKIE_NAME);
    }

    public function test_user_can_logout(): void
    {
        $data = [
            'code' => "AD20250000",
            'password' => "12345678"
        ];
        $message = [
            'message' => 'Successfully logged out'
        ];

        $log = $this->postJson('/api/login', $data);

        $cookie = $log->headers->getCookies()[0];
        $this->assertEquals(self::API_TOKEN_COOKIE_NAME, $cookie->getName());

        $response = $this->withCookie(self::API_TOKEN_COOKIE_NAME, $cookie->getValue())->postJson('/api/logout');

        // test response
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($message);
        $response->assertCookieExpired(self::API_TOKEN_COOKIE_NAME);
    }
}
