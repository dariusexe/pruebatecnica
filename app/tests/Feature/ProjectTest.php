<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use JWTAuth;
use App\Models\User;
use App\Models\Project;

class ProjectTest extends TestCase
{
    use RefreshDatabase;



    /**
     * JWT token from new user
     *
     * @return string
     */
    private function getToken(){
        $user = User::create([
            'name' => 'Darío',
            'email' => 'test@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        $token = JWTAuth::fromUser($user);

        return $token;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
    {

        

        $response = $this->get('/projects', ['Authorization' => 'Bearer ' . $this->getToken()]);

        $response->assertStatus(200);
    }
    public function test_create()
    {
        $data =
        [
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ];
        

        $response = $this->post('/projects', ['Authorization' => 'Bearer ' . $this->getToken()], $data);

        $response->assertStatus(200);
    }
    public function test_show()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $response = $this->get('/projects/' . $project->id, ['Authorization' => 'Bearer ' . $this->getToken()]);

        $response->assertStatus(200);
    }

    public function test_update()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $project_modified =
        [
            'name' => 'Project 1 modified',
            'description' => 'Project 1 description',
        ];

        $response = $this->put('/projects/' . $project->id, ['Authorization' => 'Bearer ' . $this->getToken()], $project_modified);

        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $response = $this->delete('/projects/' . $project->id, ['Authorization' => 'Bearer ' . $this->getToken()]);

        $response->assertStatus(200);
    }



}
