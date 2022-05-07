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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Project $project)
    {
        return new ActivityCollection(Auth::user()->activityFromProject($project));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        var_dump("hola");
        $activity = $project->activities()->create($request->all());
        $activity->users()->attach(Auth::user()->id, ['role_id' => UserRole::MANAGER]);
        return new ActivityResource($activity);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Activity $activity)
    {
        $this->authorize('show', $activity);
        $activity = Activity::find($activity->id);
        return new ActivityResource($activity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        $activity = Activity::find($activity->id);
        $activity->update($request->all());
        return new ActivityResource($activity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $activity = Activity::find($activity->id);
        $activity->delete();
        return new ActivityResource($activity);
    }


    public function addParticipant(Request $request, Activity $activity, Project $project){
        $activity = Activity::find($activity->id);
        $activity->participants()->attach($request->user_id);
    }
}
