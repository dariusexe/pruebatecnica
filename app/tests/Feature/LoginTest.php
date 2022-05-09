<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

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

    public function test_login_with_incorrect_credentials()
    {
        $data = [
            "email" => "some",
            "password" => "wrong"
        ];

        $response = $this->postJson('api/login', $data);

        $response->assertStatus(401);
        $response->assertJson(["error" => "invalid credentials"]);
    }
}
