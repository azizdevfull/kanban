<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
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

        if (!$code || $request->code !== $code) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification code'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        Cache::forget($key);

        // Return success response with message
        return response()->json([
            'status' => true,
            'message' => 'Email verified'
        ], 200);
    }
}
