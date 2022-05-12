<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Resources\ActivityCollection;
use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display all activities from user
     *
     * @param Request $request
     * @param Project $project
     * @return ActivityCollection
     */
    public function index(Request $request, Project $project)
    {
        return new ActivityCollection(Auth::user()->activityFromProject($project));
    }

    /**
     * Store a newly created Activity attached to a user with role Manager.
     *
     * @param  Request  $request
     * @param  Project  $project
     * @return ActivityResource
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create_activity', $project);
        $activity = $project->activities()->create($request->all());
        $activity->users()->attach(Auth::user()->id, ['role_id' => UserRole::MANAGER]);

        return new ActivityResource($activity);
    }

    /**
     * Display the specified Activity.
     *
     * @param  Activity  $activity
     * @return ActivityResource
     */
    public function show(Project $project, Activity $activity)
    {
        $this->authorize('show', $activity);
        return new ActivityResource($activity);
    }

    /**
     * Update the specified Activity in storage.
     *
     * @param  Request  $request
     * @param  Activity  $activity
     * @return ActivityResource
     */
    public function update(Request $request, Activity $activity)
    {
        $this->authorize('update', $activity);
        $activity->update($request->all());
        return new ActivityResource($activity);
    }

    /**
     * Remove the specified Activity from storage.
     *
     * @param  Activity  $activity
     * @return ActivityResource
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return new ActivityResource($activity);
    }

    /**
     * Display all users from activity
     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @return UserCollection
     */

    public function getParticipants(Request $request, Project $project, Activity $activity)
    {
        $this->authorize('show', $activity);
        return new UserCollection($activity->users);
    }

    /**
     * Add especific user to an Activity.
     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @return UserResource
     */
    public function addParticipant(Request $request, Project $project, Activity $activity)
    {
        $this->authorize('add_participant', $activity);
        $user = User::findOrFail($request->user_id);
        if ($activity->isParticipant($user)) {
            return response()->json(['error' => 'User already has been added'], 400);
        }
        $activity->users()->attach($user->id, ['role_id' => $request->role_id]);
        return response()->json(['success' => 'User added to a Activity'], 201);
    }

    /**
     * Remove especific user to an Activity.
     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @return UserResource
     */
    public function removeParticipant(Request $request, Project $project, Activity $activity, User $user)
    {
        $this->authorize('remove_participant', $activity);
        if (!$activity->isParticipant($user)) {
            return response()->json(['error' => 'User not found in Activity'], 400);
        }
        $activity->users()->detach($user->id);
        return response()->json(['success' => 'User removed from a Activity'], 200);
    }

    /**
     * Display all activities from user
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function changeParticipantRole(Request $request, Project $project, Activity $activity, User $user){
        $this->authorize('change_participant', $activity);
        if (!$activity->isParticipant($user)) {
            return response()->json(['error' => 'User not found in Activity'], 400);
        }
        $activity->users()->updateExistingPivot($user->id, ['role_id' => $request->role_id]);
        return response()->json(['success' => 'User role changed in a Activity'], 200);
    }
}
