<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->query('limit', 100);
        $forums = Forum::paginate($limit);
        return response()->json($forums, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'description' => 'required',
            'user_id' => 'required'
        ]);

        $forum = Forum::create($request->all());
        return response()->json($forum, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Forum $forum)
    {
        return response()->json($forum, 200);
    }

    public function showUserForums(string $id)
    {
        $forum =
            Forum::where('user_id', $id)
            ->orderBy('created_at', 'desc')->get();

        return response()->json($forum, 200);
    }

    public function filter(string $type)
    {
        $forum = Forum::where('type', $type)->orderBy('created_at', 'desc')->get();
        return response()->json($forum, 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Forum $forum)
    {
        $forum->update($request->all());
        return response()->json($forum, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Forum $forum)
    {
        $forum->delete();
        return response()->json(null, 204);
    }
}
