<?php

namespace App\Http\Controllers;

use App\Helpers\NotifyHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Notifications\PasswordResetRequestNotification;
use App\Notifications\PasswordResetSuccessfulNotification;

class AuthController extends Controller
{
    public function resetPassword(Request $request)
    {
        $request->validate(['new_password' => ['required', 'string', 'min:8']]);

        $user = User::query()
            ->where('email', $request->email)
            ->where('access_code', $request->access_code);

        if (!$user->count()) {
            return ResponseHelper::badRequest("Invalid email and access code combination");
        }

        $update = $user->update([
            'password' => bcrypt($request->new_password),
            'access_code' => null
        ]);

        if (!$update) {
            return ResponseHelper::serverError("Cant reset password reset at this time");
        }

        try {
            $user->first()->notify(new PasswordResetSuccessfulNotification());
        } catch (\Throwable $th) {
        }

        return ResponseHelper::sendSuccess("Password reset successfully");
    }

    public function initiatePasswordReset(Request $request)
    {
        $user = User::where('email', $request->email);

        if (!$user->count()) {
            return ResponseHelper::badRequest("Invalid email address");
        }

        $access_code = time();
        $user = $user->first();
        $updateAccesscode = $user->update(['access_code' => $access_code]);

        if (!$updateAccesscode) {
            return ResponseHelper::serverError("Cant initiate password reset at this time");
        }

        try {
            $user->notify(new PasswordResetRequestNotification($access_code));
        } catch (\Throwable $th) {
            return ResponseHelper::serverError("Cant initiate password reset at this time");
        }

        return ResponseHelper::sendSuccess("An email containing reset code has been sent to the user");
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
        ]);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if (!$user) {
            return ResponseHelper::serverError();
        }

        $token = $user->createToken('user');
        $success['token'] = $token->plainTextToken;
        $success['message'] = "Registration successfull..";

        NotifyHelper::talkTo($user,  "account_creation");
        return ResponseHelper::sendSuccess($success);
    }

    //login
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'email' => 'required|string|email|min:5'
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return ResponseHelper::unAuthorised("Incorrect email and password combination");
        }

        $user = Auth::user();
        $token = $user->createToken('user')->plainTextToken;

        NotifyHelper::talkTo($user,  "login");
        return ResponseHelper::sendSuccess(
            ['token' => $token, 'user' => $user],
            "logged in successfully"
        );
    }

    //logout
    public function logout(Request $request)
    {
        $isUser = $request->user()->currentAccessToken()->delete();
        if (!$isUser) {
            return ResponseHelper::serverError();
        }

        return ResponseHelper::sendSuccess([], "Successfully logged out.");
    }
}
