<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Resources\IncidentCollection;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project, Activity $activity)
    {
        $this->authorize('show_incident', $activity);
        return new IncidentCollection( $activity->incidentsWhereUserIsManagerInActivity(Auth::user()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project, Activity $activity)
    {
        $this->authorize('create_incident', $activity);
        $incident = $activity->incidents()->create($request->all());
        return new IncidentResource($incident);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('show_incident', $activity);
        return new IncidentResource($incident);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, Activity $activity,  Incident $incident)
    {
        $this->authorize('update_incident', $activity);
        $incident->update($request->all());
        return new IncidentResource($incident);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('delete_incident', $activity);
        $incident->delete();
        return new IncidentResource($incident);
    }

    public function addParticipant(Request $request, Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('add_participant', $activity);
        $incident->users()->attach($request->user_id);
        return new IncidentResource($incident);
    }
    public function removeParticipant(Request $request, Project $project, Activity $activity, Incident $incident)
    {
        $this->authorize('remove_participant', $activity);
        $incident->users()->detach($request->user_id);
        return new IncidentResource($incident);
    }
}
