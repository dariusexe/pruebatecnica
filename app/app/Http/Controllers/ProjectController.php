<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Enum\UserRole;
use Illuminate\Support\Facades\Auth;
use App\Models\Permissions;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\UserCollection;
use App\Models\User;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\ProjectCollection
     */
    public function index(Request $request)
    {
        $projects = Project::all();
        return new ProjectCollection($projects);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $project = Project::create($request->all());
        $project->users()->attach($request->user()->id, ['role_id' => UserRole::ADMIN]);
        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
       return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $project = Project::findOrFail($project->id);
        $project->update($request->all());
        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project = Project::findOrFail($project->id);
        $project->delete();
        return new ProjectResource($project);
    }

    /**
     * Add especific user to a Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function addParticipant(Request $request, Project $project)
    {

        $role = $request->role_id;
        $user = User::findOrFail($request->user_id);
        try {
            if($project->isParticipantWithRole($user, $role)){
                return response()->json(['error' => 'User already exists in project'], 400);
            }
            $project->users()->attach($user, ['role_id' => $role]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['success' => 'User added to project'], 201);
    }

    /**
     * Remove especific user from Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function removeParticipant(Request $request, Project $project, User $user)

    {
        $project = Project::findOrFail($project->id);
        try {
            $project->users()->detach($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['success' => 'User removed from project'], 200);
    }

    /**
     * Display all Participan users in a Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function getParticipants(Project $project)
    {
        $project = Project::findOrFail($project->id);
        $participants = $project->users()->where('role_id', UserRole::PARTICIPANT)->get();
        return new UserCollection($participants);
    }

    /**
     * Display all Manager users in a Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function getManagers(Project $project)
    {
        $project = Project::findOrFail($project->id);
        $managers = $project->users()->where('role_id', UserRole::ADMIN)->get();
        return new UserCollection($managers);

    }
}
