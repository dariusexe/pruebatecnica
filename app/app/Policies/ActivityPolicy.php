<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use App\Enum\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function show(User $user, Activity $activity){
        return $activity->isParticipant($user);
    }
    public function update(User $user, Project $project){
        return $project->isParticipantWithRole($user, UserRole::MANAGER);
    }
}
