<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\UserSkills;
use Illuminate\Http\Request;

class UserSkillsController extends Controller
{
    protected $message = "Skills sync successfully";

    public function syncSkills(Request $request)
    {
        $request->validate(["skillsIds" => "required|array|exists:sub_tasks,id"]);
        $skillObject = [];
        $requestSkillsID = $request->skillsIds;

        // delete what user doesnt want from the old skills/
        $oldSkills = UserSkills::query()
            ->where('user_id', $request->user()->id)
            ->pluck('sub_task_id');

        if (!$oldSkills->count()) {
            $skillsUpdate = $this->createSkillsFromArray($requestSkillsID, $request->user());

            return $skillsUpdate ?
                ResponseHelper::sendSuccess([], $this->message) : ResponseHelper::serverError();
        }

        // skills present in old but not in request
        $unwantedSkillsId = $oldSkills->diff($requestSkillsID)->values();
        if ($unwantedSkillsId->count()) {
            UserSkills::query()->whereIn('sub_task_id', $unwantedSkillsId)->delete();
        }

        // skills present in request but not in old
        $newSkillsId = collect($requestSkillsID)->diff($oldSkills)->values();
        if (!$newSkillsId->count()) {
            return ResponseHelper::sendSuccess([], $this->message);
        }

        $skillsUpdate = $this->createSkillsFromArray($newSkillsId, $request->user());
        return $skillsUpdate ?
            ResponseHelper::sendSuccess([], $this->message) : ResponseHelper::serverError();
    }

    public function userSkills(Request $request)
    {
        $skills = $request->user()->skills();

        if (!$skills->count()) {
            return ResponseHelper::notFound("You do not have any skills at the moment");
        }

        $skills = $skills->with('skill:id,name')->get();
        $skills = $skills->map(function ($item, $key) {
            return $item->skill;
        });

        return ResponseHelper::sendSuccess($skills);
    }

    private function createSkillsFromArray($skillIds, User $user)
    {
        foreach ($skillIds as $value) {
            $skillObject[] = [
                'sub_task_id' => $value
            ];
        }
        return $user->skills()->createMany($skillObject);
    }
}
