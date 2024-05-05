<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'github' => 'required',
            'url' => 'required',
            'user_id' => 'required'
        ]);

        $project = Project::create($request->all());
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function showUserProjects(string $id)
    {
        $project =
            Project::where('user_id', $id)
            ->orderBy('created_at', 'desc')->get();

        return response()->json($project, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::find($id);
        $project->update($request->all());
        return response()->json($project, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Project::destroy($id);
        return response()->json(null, 204);
    }
}
