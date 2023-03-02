<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $task = $user->tasks;
        $biriktirilgan_vazifalar = Task::whereHas('assignedTo', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'user' => $user,
            'biriktirilgan_vazifalar' => $biriktirilgan_vazifalar
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the profile photo file
        $request->validate([
            'name' =>'nullable|string',
            'bio' => 'nullable',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Save the new profile photo file (if provided)
        if ($request->hasFile('photo')) {
            $profilePhoto = $request->file('photo');
            $profilePhotoPath = $profilePhoto->store('profile_photos', 'public');
            $user->photo = $profilePhotoPath;
        }

            if($request->input('name')){
                $user->name = $request->input('name');
            }
            else if($request->input('bio')){
                $user->bio = $request->input('bio');
            }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user
        ], 200);
    }

}
