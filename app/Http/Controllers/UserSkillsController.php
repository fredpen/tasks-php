<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\UserSkills;
use Illuminate\Http\Request;

class UserSkillsController extends Controller
{
    public function syncSkills(Request $request)
    {
        $request->validate(["skillsId" => "required|array"]);

        // return $request->skillsId;
        // return UserSkills::query()
        // ->where('user_id', $request->user()->id)
        // ->get();
        return $update = $request->user()->skills()->create($request->skillsId);

        return $update ?
            ResponseHelper::sendSuccess([], "Skills sync successfully") : ResponseHelper::serverError();
    }

    public function userSkills(Request $request)
    {
        $skills = $request->user()->skills();

        return $skills ?
            ResponseHelper::sendSuccess($skills->with('skill')->get()) : ResponseHelper::serverError();
    }
}
