<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'role_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'phone_number' => ['required', 'int', 'unique:users', 'min:9'],
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

    //getuser
    public function getUser(Request $request)
    {
        return ResponseHelper::sendSuccess($request->user());
    }

    //update User
    public function updateUser(Request $request)
    {
        $unallowedFields = $this->validateUpdateRequest($request->all());
        if (count($unallowedFields)) {
            return ResponseHelper::badRequest("You can not update $unallowedFields[0]");
        }

        $validatedData = $request->validate([
            'name' => ['sometimes','required','string', 'min:3'],
            'title' => ['sometimes','required','string', 'min:3'],
            'phone_number' => ['sometimes','required', 'numeric'],
            'country_id' => ['sometimes','required', 'exists:countries,id'],
            'region_id' => ['sometimes','required', 'exists:regions,id'],
            'city_id' => ['sometimes','required', 'exists:cities,id'],
            'address' => ['sometimes','required','string', 'min:3'],
            "linkedln" => ['sometimes','required', 'string', 'min:3'],
            "bio" => ['sometimes','required','string', 'min:3'],
            "email" => ['sometimes','required', 'email', 'unique:']
        ]);

        $update = $request->user()->update($validatedData);

        return $update ? ResponseHelper::sendSuccess([], "Update successful") : ResponseHelper::serverError();
    }

    private function validateUpdateRequest($request)
    {
        $allowedFields = Config::get('constants.userUpdate');
        $incomingFields = array_keys($request);
        $notAllowed = array_filter($incomingFields, function ($incomingField) use ($allowedFields) {
            return !in_array($incomingField, $allowedFields);
        });
        return array_values($notAllowed);
    }
}
