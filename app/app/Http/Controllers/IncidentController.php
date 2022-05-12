<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Resources\IncidentCollection;
use App\Http\Resources\IncidentResource;
use App\Http\Resources\UserCollection;
use App\Models\Incident;
use App\Models\Project;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Project  $project
     * @param  Request  $request
     * @return IncidentCollection
     */
    public function index(Project $project, Activity $activity)
    {
        $this->authorize('show_incident', $activity);
        return new IncidentCollection( Auth::user()->incidentsWhereUserIsManagerInActivity($activity) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Project  $project
     * @param  Request  $request
     * @return IncidentResource
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
     * @param  Project  $project
     * @param  Request  $request
     * @return IncidentResource
     */
    public function show(Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('show_incident', $activity);
        return new IncidentResource($incident);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Project  $project
     * @param  Request  $request
     * @return IncidentResource
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
     * @param  Project  $project
     * @param  Request  $request
     * @return IncidentResource
     */
    public function destroy(Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('delete_incident', $activity);
        $incident->delete();
        return new IncidentResource($incident);
    }

    /**
     * Add especific user to a Incident
     *
     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @param Incident $incident
     * @return Response
     */


    public function addParticipant(Request $request, Project $project, Activity $activity, Incident $incident)
    {

        $this->authorize('add_participant', $activity);
        $user = User::findOrFail($request->user_id);
        $incident->users()->attach($user->id);
        return response()->json(['success' => 'User added to a Activity'], 201);
    }


    /**
     * Remove a Participant from a Incident

     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @param Incident $incident
     * @param User $user
     * @return Response
     */
    public function removeParticipant(Request $request, Project $project, Activity $activity, Incident $incident, User $user)
    {
        $this->authorize('remove_participant', $activity);
        $incident->users()->detach($user->id);
        return response()->json(['success' => 'User removed to a Activity'], 200);
    }

    /**
     * Return all participants in a Incident
     *
     * @param Request $request
     * @param Project $project
     * @param Activity $activity
     * @param Incident $incident
     * @return UserCollection
     */
    public function getParticipants(Request $request, Project $project, Activity $activity, Incident $incident)
    {
        $this->authorize('show_participants', $activity);
        return new UserCollection($incident->users);
    }
}
