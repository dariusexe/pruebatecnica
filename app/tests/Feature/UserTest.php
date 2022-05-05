<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */


     // user data
    private $user_data = [
        'name' => 'Darío',
        'email' => 'test@gmail.com',
        'password' => '123456',
        'password_confirmation' => '123456'
    ];
     

    public function testRegister()
    {


        $response = $this->json('post', '/register', $this->user_data);
        $response->assertStatus(201);
        

    }

    public function testLogin()
    {
        $response = $this->json('post', '/register', $this->user_data);

        $credentials = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password']
        ];

        $response = $this->json('post', '/login', $credentials);
        
        $response->assertStatus(200);
    }

}
