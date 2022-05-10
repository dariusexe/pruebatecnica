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
        $activity_factory = Activity::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER]);

        $project = Project::factory()->has($activity_factory)->create();

        $activity = $project->activities()->first();

        $response = $this->getJson('api/project/' . $project->id . '/activities');
        $response->assertStatus(200)
        ->assertJsonFragment(['id' => $activity->id]);
    }
    

    public function test_not_found_activity_when_scope_binding_fails(){
        $activity_factory = Activity::factory();
        $project = Project::factory()->has($activity_factory)->create();
        $activity = $project->activities()->first();
        $response = $this->getJson('api/project/' . $project->id + 1 . '/activities/'.$activity->id);
        $response->assertStatus(404);
    }

    public function test_create_activity(){
        $project = Project::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER])->create();

        $data = [
            'name' => 'test project'
        ];

        $response = $this->postJson('api/project/'.$project->id. '/activities' , $data);

        $response->assertStatus(201)
        ->assertJsonFragment($data);
    }
    public function test_user_only_can_be_manager_or_participant(){
        $activity_factory = Activity::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER]);
        $project = Project::factory()->has($activity_factory)->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER])->create();
        $activity = $project->activities()->first();
        $data = [
            'user_id'=> $this->loggedInUser->id,
            'role_id'=> UserRole::PARTICIPANT
        ];
        $response = $this->postJson('api/projects/'.$project->id.'/activities/'.$activity->id.'/users', $data);
        $response->assertStatus(400)
        ->assertJson(['error' => 'User already has been added']);

    }
}
