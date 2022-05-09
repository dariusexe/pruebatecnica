<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Project;
use Database\Factories\ActivityFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_all_activites_from_user_and_project()
    {
        $activity = Activity::factory()->hasAttached($this->loggedInUser, ['role_id' => 1]);

        $project = Project::factory()->has($activity)->create();

        $response = $this->getJson('api/project/' . $project->id . '/activities');
        $response->assertStatus(200)
        ->assertJsonFragment(['id' => $project->activities()->get()[0]->id]);
    }
}
