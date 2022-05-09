<?php

namespace App\Policies;

use App\Enum\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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

    public function show(User $user, Project $project)
    {
        return $project->isParticipant($user);
    }
    public function create_activity(User $user, Project $project)
    {
        return $project->isParticipant($user);
    }

    public function delete(User $user, Project $project)
    {
        return $project->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function update(User $user, Project $project)
    {
        return $project->isParticipantWithRole($user, UserRole::MANAGER);
    }

    public function edit_participant(User $user, Project $project)
    {
        return $project->isParticipantWithRole($user, UserRole::MANAGER);
    }
}
