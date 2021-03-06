<?php

namespace Tests\Feature;

use App\Enum\UserRole;
use App\Models\Activity;
use App\Models\Incident;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncidentTest extends TestCase
{

    use RefreshDatabase;


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_only_manager_of_activity_can_show_incidents()
    {

        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents');
        $response->assertStatus(200)->assertJsonFragment($this->incident->toArray());

        $this->activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::PARTICIPANT]);

        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents');
        $response->assertStatus(403);
    }
    public function test_only_manager_of_activity_can_create_incident()
    {

        $data = [
            'name' => 'test incident'
        ];
        $response = $this->postJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents', $data);
        $response->assertStatus(201)->assertJsonFragment($data);
        $this->activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->postJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents', $data);
        $response->assertStatus(403);
    }

    public function test_only_manager_of_activity_can_show_incident()
    {
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id);
        $response->assertStatus(200)->assertJsonFragment($this->incident->toArray());
        $this->activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id);
        $response->assertStatus(403);
    }
    public function test_only_manager_of_activity_can_update_incident()
    {
        $data = [
            'name' => 'test incident'
        ];
        $response = $this->putJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id, $data);
        $response->assertStatus(200)->assertJsonFragment($data);
        $this->activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->putJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id, $data);
        $response->assertStatus(403);
    }

    public function test_only_manager_of_activity_can_delete_incident()
    {
        $project = Project::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER])->has(Activity::factory()->has(Incident::factory())->hasAttached($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]))->create();
        $activity = $project->activities()->first();
        $incident = $activity->incidents()->first();

        $response = $this->deleteJson('api/projects/' . $project->id . '/activities/' . $activity->id . '/incidents/' . $incident->id);
        $response->assertStatus(403);
        $activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::MANAGER]);
        $response = $this->deleteJson('api/projects/' . $project->id . '/activities/' . $activity->id . '/incidents/' . $incident->id);
        $response->assertStatus(200);
    }
    public function test_show_incorrect_incident(){
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id . '1');
        $response->assertStatus(404);
    }

    public function test_show_correct_incidents(){
        $project = Project::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER])->has(Activity::factory()->has(Incident::factory())->hasAttached($this->loggedInUser, ['role_id' => UserRole::PARTICIPANT]))->create();
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents');
        $response->assertStatus(200)->assertJsonMissing(['id' => $project->activities()->first()->incidents()->first()->id]);
    }
    public function test_add_participant_to_incident(){
        $response = $this->postJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id . '/users', ['user_id' => $this->user->id]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users-incidents', ['incident_id' => $this->incident->id, 'user_id' => $this->user->id]);
    }
    public function test_remove_participant_from_incident(){
        $this->incident->users()->attach($this->user->id);
        $response = $this->deleteJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id . '/users/'.$this->user->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users-incidents', ['incident_id' => $this->incident->id, 'user_id' => $this->user->id]);
    }
    public function test_show_participants_of_incident(){
        $this->incident->users()->attach($this->user->id);
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents/' . $this->incident->id . '/users');
        $response->assertStatus(200)->assertJsonFragment(['id' => $this->user->id]);
    }
}
