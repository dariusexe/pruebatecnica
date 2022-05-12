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
    public function show(User $user, Activity $activity)
    {
        return $activity->isParticipant($user);
    }

    public function show_incident(User $user, Activity $activity)
    {

        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function create_incident(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function edit_incident(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function delete_incident(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function update_incident(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function show_participants(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function add_participant(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function remove_participant(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
    public function change_participant(User $user, Activity $activity)
    {
        return $activity->isParticipantWithRole($user, UserRole::MANAGER);
    }
}
