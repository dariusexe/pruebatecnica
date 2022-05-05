<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Enum\UserRole;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::all();
        return $projects;
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
        return $project;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project = Project::findOrFail($project->id);
        return $project;
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
        return $project;
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
        return $project;
    }

    public function addParticipant(Request $request, Project $project)
    {
        $project = Project::findOrFail($project->id);
        try {
            $project->users()->attach($request->user_id, ['role_id' => UserRole::PARTICIPANT]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return $project;
    }

    public function removeParticipant(Request $request, Project $project)
    {
        $project = Project::findOrFail($project->id);
        try {
            $project->users()->detach($request->user_id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return $project;
    }
    public function getParticipants(Project $project)
    {
        $project = Project::findOrFail($project->id);
        return $project->users;}

}
