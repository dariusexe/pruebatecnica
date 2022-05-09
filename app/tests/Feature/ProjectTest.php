<?php

namespace Tests\Feature;

use App\Enum\UserRole;
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
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_only_project_with_user_participant()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $project->users()->attach($this->user, ['role_id' => UserRole::MANAGER]);

        $response = $this->get('api/projects', $this->headers);

        $response->assertStatus(200);
        $response->assertJson([]);
    }
    public function test_create_correct_project()
    {
        $data =
            [
                'name' => 'Project 1',
                'description' => 'Project 1 description',
            ];


        $response = $this->postJson('api/projects', $data, $this->headers);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
    }
    public function test_cannot_show_project_without_permission()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
        $project->users()->attach($this->user, ['role_id' => UserRole::MANAGER]);

        $response = $this->get('api/projects/' . $project->id, $this->headers);

        $response->assertStatus(403);
    }

    public function test_update_project()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);

        $project_modified =
            [
                'name' => 'Project 1 modified',
                'description' => 'Project 1 description',
            ];

        $response = $this->putJson('api/projects/' . $project->id, $project_modified, $this->headers);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Project 1 modified'
        ]);
    }

    public function test_delete_correct_project()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);

        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);

        $response = $this->deleteJson('api/projects/' . $project->id, $this->headers);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Project 1'
        ]);
    }

    public function test_cannot_delete_project_without_permission()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
        $project->users()->attach($this->user, ['role_id' => UserRole::MANAGER]);

        $response = $this->delete('api/projects/' . $project->id, $this->headers);
        $response->assertStatus(403);
    }

    public function test_user_can_participate_and_manage()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->getJson('api/projects/' . $project->id, $this->headers);
        $response->assertStatus(200);
        $response->assertJsonPath('data.managers.0.id', $this->loggedInUser->id);
        $response->assertJsonPath('data.participants.0.id', $this->loggedInUser->id);
    }

    public function test_user_cannot_be_added_with_same_role()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);
        $data = [
            'user_id' => $this->loggedInUser->id,
            'role_id' => UserRole::MANAGER
        ];
        $response = $this->postJson('api/projects/' . $project->id . '/users', $data,  $this->headers);
        $response->assertStatus(400);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]);
        $data = [
            'user_id' => $this->loggedInUser->id,
            'role_id' => UserRole::PARTICIPANT
        ];
        $response = $this->postJson('api/projects/' . $project->id . '/users', $data,  $this->headers);
        $response->assertStatus(400);
    }

    public function test_only_manager_can_edit_participants()
    {
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project 1 description',
        ]);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]);
        $data = [
            'user_id' => $this->user->id,
            'role_id' => UserRole::PARTICIPANT
        ];
        $response = $this->postJson('api/projects/' . $project->id . '/users', $data,  $this->headers);
        $response->assertStatus(403);
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);
        $response = $this->postJson('api/projects/' . $project->id . '/users', $data,  $this->headers);
        $response->assertStatus(201);
    }
}
