<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function Home()
    {
        $tasks = Task::all()->count();
        $u_tasks = Task::where('user_id', Auth::user()->id)->count();
        return response()->json([
            'all_tasks_count' => $tasks,
            'your_tasks_count' => $u_tasks
        ]);
    }
}
