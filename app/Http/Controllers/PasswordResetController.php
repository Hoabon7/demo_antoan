<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;

use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class PasswordResetController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' =>'required'
        ]);
        $user = User::where('email', $request->email)
        ->orWhere('password',$request->password)
        ->first();
        // echo "<pre>";
        // var_dump($user->password);
        // echo "</pre>";
        if (!$user)
            return response()->json(['message' => "Pass or Mail Don't exits"
            ], 404);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'password_reset'=>$user->password,
                'token' => str_random(60)
             ]
        );
        if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($user->email,$passwordReset->token)
            );
        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    public function find(Request $request)
    {
        $passwordReset = PasswordReset::where(
            [
                ['token',$request->email],
                ['email',$request->token]
            ]
           )
            ->first();
            // echo "<pre>";
            // var_dump($request->email);
            // echo "<pre>";
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return view('auth.resetpass')->with('passwordReset',$passwordReset);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'email_reset' => 'required|string|email',
            'password_reset' => 'required|string',
            'password_confirm_reset'=>'required|string',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email_reset]
        ])->first();

        echo "<pre>";
        var_dump($passwordReset);
        echo "</pre>";
       
       
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'message' => "We can't find a user with that e-mail address."
            ], 404);
        $user->password = bcrypt($request->password_reset);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }
}
