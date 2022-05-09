<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */


    public function test_login_with_credentials()
    {

        $data = [
            "email" => $this->user->email,
            "password" => "password"
        ];

        $response = $this->json('post', 'api/login', $data);

        $response->assertStatus(200);
    }

    public function test_it_returns_field_required_validation_errors_on_invalid_login()
    {
        $data = [];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            "password" => [
                "The password field is required."
            ],
            "email" => [
                "The email field is required."
            ]
        ]);
    }

    public function test_login_with_incorrect_credentials()
    {
        $data = [
            "email" => "wrong@gmail.com",
            "password" => "some"
        ];

        $response = $this->postJson('api/login', $data);

        $response->assertStatus(401);
        $response->assertJsonFragment(['error' => 'invalid credentials']);
    }

}
