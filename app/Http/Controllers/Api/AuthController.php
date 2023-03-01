<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $code = mt_rand(10000, 99999);

        $key = 'email_verification_'.$request->email;
        Cache::put($key, $code, now()->addMinutes(5));

        Mail::send('emails.verification', ['code' => $code, 'email' => $request->email], function($message) use ($request) {
            $admin_gmail = "aziz16110904@gmail.com";
                $message->to($request->email)->subject('Email Verification Code');
                $message->from(config('mail.from.address'), config('mail.from.name'));
            });

        // Return success response with message
        return response()->json([
            'status' => true,
            'message' => 'Email verification code sent'
        ], 200);
    }

    public function verifyEmail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'code' => 'required|string|max:5',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $key = 'email_verification_'.$request->email;
    $code = Cache::get($key);

    if (!$code || $request->code != $code) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid verification code'
        ], 422);
    }

    $user = User::where('email', $request->email)->first();
    $user->email_verified_at = now();
    $user->save();

    Cache::forget($key);

    // Generate API token for the authenticated user
    $token = $user->createToken('api_token')->plainTextToken;

    // Return success response with message and token
    return response()->json([
        'status' => true,
        'message' => 'Email verified',
        'token' => $token,
        'user' => $user
    ], 200);
}

public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    if (!$user->email_verified_at) {
        return response()->json([
            'status' => false,
            'message' => 'Email not verified'
        ], 401);
    }

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user
    ], 200);
}


public function forgotPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Email address not found'
        ], 404);
    }

    $code = mt_rand(10000, 99999);

    $key = 'reset_password_'.$request->email;
    Cache::put($key, $code, now()->addMinutes(5));

    Mail::send('emails.reset-password', ['code' => $code, 'email' => $request->email], function($message) use ($request) {
        $message->to($request->email)->subject('Reset Password Code');
        $message->from(config('mail.from.address'), config('mail.from.name'));
    });

    // Return success response with message
    return response()->json([
        'status' => true,
        'message' => 'Password reset code sent'
    ], 200);
}

public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'code' => 'required|string|max:5',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $key = 'reset_password_'.$request->email;
    $code = Cache::get($key);

    if (!$code || $request->code != $code) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid reset code'
        ], 422);
    }

    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    Cache::forget($key);

    // Generate API token for the authenticated user
    $token = $user->createToken('api_token')->plainTextToken;

    // Return success response with message and token
    return response()->json([
        'status' => true,
        'message' => 'Password reset successful',
        'token' => $token,
        'user' => $user
    ], 200);
}

public function logoutUser(Request $request)
{
    //  use Laravel\Sanctum\PersonalAccessToken;


// Get bearer token from the request
$accessToken = $request->bearerToken();

// Get access token from database
$token = PersonalAccessToken::findToken($accessToken);

// Revoke token
$token->delete();

return response()->json([
    'status' => true,
    'message' => 'User logged out successfully!',
], 200);

}

}
