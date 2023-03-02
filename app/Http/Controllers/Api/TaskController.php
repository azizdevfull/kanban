<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('users')->get();

        return response()->json(['data' => $tasks], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        // If validation fails, return error message
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create new task
        $task = new Task;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->user_id = Auth::user()->id;
        $task->save();

        // Return success message
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    public function suggest(Request $request, $id)
{
    $task = Task::find($id);

    $user_id = $request->user_id;
    $user = User::find($user_id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $task->suggestedUsers()->attach($user_id);

    return response()->json(['message' => 'User suggested successfully'], 200);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id)->with('users')->get();
        return response()->json(['task' => $task], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
