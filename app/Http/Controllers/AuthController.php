<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use App\Models\User;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            // 'role_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
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
        return ResponseHelper::sendSuccess(['token' => $token], "logged in successfully");
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
