<?php

namespace Tests\Feature;

use App\Enum\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_only_manager_of_activity_can_show_incidents(){
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents');
        $response->assertStatus(200)->assertJsonFragment($this->incident->toArray());
        $this->activity->users()->updateExistingPivot($this->loggedInUser->id, ['role_id' => UserRole::PARTICIPANT]);
        $response = $this->getJson('api/projects/' . $this->project->id . '/activities/' . $this->activity->id . '/incidents');
        $response->assertStatus(403);

    }

    public function test_user_show_projects(){
        $response = $this->getJson('api/users/' . $this->loggedInUser->id . '/projects');

        $response->assertStatus(200)->assertJsonFragment($this->project->toArray());
    }

    public function test_user_show_activities(){
        $response = $this->getJson('api/users/' . $this->loggedInUser->id . '/activities');
        $response->assertStatus(200)->assertJsonFragment($this->activity->toArray());
    }
}
