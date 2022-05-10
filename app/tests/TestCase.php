<?php

namespace Tests;

use App\Enum\UserRole;
use App\Models\Activity;
use App\Models\Incident;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $loggedInUser;
    protected $user;
    protected $headers;
    protected $project;
    protected $activity;
    protected $incident;


    public function setUp() : void
    {
        parent::setUp();
        $users = User::factory()->count(2)->create();
        $this->loggedInUser = $users[0];
        $this->user = $users[1];
        $this->headers = [
            'Authorization' => 'Bearer ' . Auth::attempt(['email' => $this->loggedInUser->email, 'password' => 'password'])
        ];
        $this->project = Project::factory()->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER])->has(Activity::factory()->has(Incident::factory())->hasAttached($this->loggedInUser, ['role_id' => UserRole::MANAGER]))->create();
        $this->activity = $this->project->activities()->first();
        $this->incident = $this->activity->incidents()->first();
    }
}
