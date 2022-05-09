<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Activity;

class IncidentPolicy
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
    public function showAll(User $user, Activity $activity){
        return $activity->isManager($user);
    }
    public function show(User $user, Activity $activity){
        return $activity->isManager($user);
    }
    public function create(User $user, Activity $activity){
        return $activity->isManager($user);
    }
}
