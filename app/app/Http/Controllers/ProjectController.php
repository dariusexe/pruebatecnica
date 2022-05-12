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
     * @param  Request  $request
     * @return ProjectCollection
     */
    public function index(Request $request)
    {

        return new ProjectCollection(Auth::user()->projects);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return ProjectResource
     */
    public function store(Request $request)
    {
        $project = Project::create($request->all());
        $project->users()->attach(Auth::user()->id, ['role_id' => UserRole::MANAGER]);
        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  Project  $project
     * @return ProjectResource
     */
    public function show(Project $project)
    {
        $this->authorize('show', $project);
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Project  $project
     * @return ProjectResource
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->update($request->all());
        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Project  $project
     * @return ProjectResource
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return new ProjectResource($project);
    }

    /**
     * Add especific user to a Project.
     *
     * @param  Request  $request
     * @param  Project  $project
     * @return Response
     */
    public function addParticipant(Request $request, Project $project)
    {
        $this->authorize('edit_participant', $project);

        $role = $request->role_id;
        $user = User::findOrFail($request->user_id);
        try {
            if ($project->isParticipantWithRole($user, $role)) {
                return response()->json(['error' => 'User already exists in project with this role'], 400);
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
     * @param  Request  $request
     * @param  Project  $project
     * @return Response
     */
    public function removeParticipant(Request $request, Project $project, User $user)

    {
        $this->authorize('edit_participant', $project);
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
     * @param  Request  $request
     * @param  Project  $project
     * @return Response
     */
    public function getParticipants(Project $project)
    {
        $participants = $project->users;
        return new UserCollection($participants);
    }


}
