<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Resources\ActivityCollection;
use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display all activities from user
     *
     * @param Request $request
     * @param Project $project
     * @return \App\Http\Resources\ActivityCollection
     */
    public function index(Request $request, Project $project)
    {
        return new ActivityCollection(Auth::user()->activityFromProject($project));
    }

    /**
     * Store a newly created Activity attached to a user with role Manager.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project       $project
     * @return \App\Http\Resources\ActivityResource
     */
    public function store(Request $request, Project $project)
    {
        $activity = $project->activities()->create($request->all());
        $activity->users()->attach(Auth::user()->id, ['role_id' => UserRole::MANAGER]);
        return new ActivityResource($activity);
    }

    /**
     * Display the specified Activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \App\Http\Resources\ActivityResource
     */
    public function show(Project $project, Activity $activity)
    {
        $this->authorize('show', $activity);
        $activity = Activity::find($activity->id);
        return new ActivityResource($activity);
    }

    /**
     * Update the specified Activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \App\Http\Resources\ActivityResource
     */
    public function update(Request $request, Activity $activity)
    {
        $activity = Activity::find($activity->id);
        $activity->update($request->all());
        return new ActivityResource($activity);
    }

    /**
     * Remove the specified Activity from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \App\Http\Resources\ActivityResource
     */
    public function destroy(Activity $activity)
    {
        $activity = Activity::find($activity->id);
        $activity->delete();
        return new ActivityResource($activity);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Activity $activity
     * @return \Illuminate\Http\Response
     */
    public function addParticipant(Request $request, Activity $activity)
    {
        $activity = Activity::find($activity->id);
        $activity->participants()->attach($request->user_id);
        return response()->json(['success' => 'User added to a Activity'], 200);
    }
}
