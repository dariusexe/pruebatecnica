<?php

namespace Tests\Feature;

use App\Enum\UserRole;
use App\Models\Activity;
use App\Models\Project;
use Database\Factories\ActivityFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_all_activites_from_user_and_project()
    {

        $response = $this->getJson('api/projects/' . $this->project->id . '/activities');
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $this->activity->id]);
    }


    public function test_not_found_activity_when_scope_binding_fails()
    {
        $response = $this->getJson('api/project/' . $this->project->id + 1 . '/activities/' . $this->activity->id);
        $response->assertStatus(404);
    }

    public function test_create_activity()
    {

        $data = [
            'name' => 'test project'
        ];

        $response = $this->postJson('api/projects/' . $this->project->id . '/activities', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }
    public function test_user_only_can_be_manager_or_participant()
    {
        $data = [
            'user_id' => $this->loggedInUser->id,
            'role_id' => UserRole::PARTICIPANT
        ];
        $response = $this->postJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/users', $data);
        $response->assertStatus(400)
            ->assertJson(['error' => 'User already has been added']);
    }

    public function test_user_can_be_added_to_activity()
    {
        $data = [
            'user_id' => $this->user->id,
            'role_id' => UserRole::PARTICIPANT
        ];
        $response = $this->postJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/users', $data);
        $response->assertStatus(201)
            ->assertJsonFragment(['success' => 'User added to a Activity']);
    }
    public function test_user_can_be_removed_from_activity(){

        $this->activity->users()->attach($this->user, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->deleteJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/users/'.$this->user->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['success' => 'User removed from a Activity']);
    }

    public function test_show_all_users_from_activity_with_correct_permissions(){
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/users');
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $this->loggedInUser->id]);
    }

    public function test_only_manager_can_create_activity(){
        $project = Project::factory()->create();
        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]);
        $data =[
            'name' => 'Activity 1',
        ];
        $response = $this->postJson('api/projects/' . $project->id . '/activities', $data,  $this->headers);
        $response->assertStatus(403);

        $project->users()->attach($this->loggedInUser, ['role_id' => UserRole::MANAGER]);
        $response = $this->postJson('api/projects/' . $project->id . '/activities', $data,  $this->headers);
        $response->assertStatus(201);

    }
    public function test_show_correct_incidents_from_activity_with_correct_permissions(){
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id);
        $response->assertStatus(200)
            ->assertJsonPath('data.incidents.0.id', $this->incident->id);
        $this->activity->users()->updateExistingPivot($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id);
        $response->assertStatus(200)
            ->assertJsonPath('data.incidents.0.id', null);
    }
}
