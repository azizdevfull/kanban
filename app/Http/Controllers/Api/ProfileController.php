<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $tasks = $user->tasks;
        $biriktirilgan_vazifalar = Task::whereHas('assignedTo', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'user' => $user,
            'tasks' => $tasks,
            'biriktirilgan_vazifalar' => $biriktirilgan_vazifalar
        ]);
    }
}
