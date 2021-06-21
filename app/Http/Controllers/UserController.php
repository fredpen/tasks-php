<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    public function setSecurity(Request $request)
    {
        $request->validate([
            'security_question' => 'bail|required|string|min:4',
            'security_answer' => 'bail|required|string|min:4'
        ]);

        $user = $request->user();
        if ($user->security_question) {
            return ResponseHelper::badRequest("Security question has already been set, you can only set this once");
        }

        $update = $user->update($request->only(['security_question', 'security_answer']));

        return $update ? ResponseHelper::sendSuccess([], "security question set") : ResponseHelper::serverError();
    }


    public function userDetails(Request $request)
    {
        return ResponseHelper::sendSuccess($request->user());
    }

    public function updateUser(Request $request)
    {
        $message = "use api/user/update-security-data' for updating users's name, phone_number, email and means of identification";

        $unallowedField = $this->validateUpdateRequest($request->all());
        if ($unallowedField !== true) {
            return ResponseHelper::badRequest("You can not update {$unallowedField}, {$message}");
        }

        $validatedData = $this->sanitizeRequest($request);

        if ($request->has('avatar')) {
            $url =  $request->user()
                ->storeMyFile($request->file('avatar'), 'avatars', $request->user()->id);
            $validatedData['avatar'] = $url;
        }

        $update = $request->user()->update($validatedData);

        return $update ? ResponseHelper::sendSuccess([], "Update successful") : ResponseHelper::serverError();
    }

    public function updateSecurityData(Request $request)
    {
        $validatedData = $this->sanitizeRequest($request, true);

        $user = $request->user()->makeVisible(['security_answer']);
        if ($user->security_answer != $request->security_answer) {
            return ResponseHelper::badRequest("Incorrect security answer");
        }

        if ($request->has('identification')) {
            $url =  $request->user()
                ->storeMyFile($request->file('identification'), 'identifications', $request->user()->id);
            $validatedData['identification'] = $url;
        }

        unset($validatedData['security_answer']);
        $update = $user->update($validatedData);

        return $update ? ResponseHelper::sendSuccess([], "Update successful") : ResponseHelper::serverError();
    }

    private function validateUpdateRequest($request)
    {
        $incomingFields = array_keys($request);
        $allowedFields = Config::get('constants.userUpdate');

        foreach ($incomingFields as $requestKey) {
            if (!in_array($requestKey, $allowedFields)) {
                return $requestKey;
            }
        }

        return true;
    }

    private function sanitizeRequest($request, $securityData = false)
    {
        if (!$securityData) {
            return $request->validate([
                'title' => ['sometimes', 'required', 'string', 'min:3'],
                'country_id' => ['sometimes', 'required', 'exists:countries,id'],
                'region_id' => ['sometimes', 'required', 'exists:regions,id'],
                'city_id' => ['sometimes', 'required', 'exists:cities,id'],
                'address' => ['sometimes', 'required', 'string', 'min:3'],
                "linkedln" => ['sometimes', 'required', 'string', 'min:3'],
                "bio" => ['sometimes', 'required', 'string', 'min:3'],
                "avatar" => ['sometimes', 'required', 'image', 'max:2000000'],
            ]);
        }

        return $request->validate([
            "security_answer" => ['required', 'string', 'min:3'],

            'name' => ['sometimes', 'required', 'string', 'min:3'],
            'title' => ['sometimes', 'required', 'string', 'min:3'],
            'phone_number' => ['sometimes', 'required', 'numeric'],
            'country_id' => ['sometimes', 'required', 'exists:countries,id'],
            'region_id' => ['sometimes', 'required', 'exists:regions,id'],
            'city_id' => ['sometimes', 'required', 'exists:cities,id'],
            'address' => ['sometimes', 'required', 'string', 'min:3'],
            "linkedln" => ['sometimes', 'required', 'string', 'min:3'],
            "bio" => ['sometimes', 'required', 'string', 'min:3'],
            "email" => ['sometimes', 'required', 'email', 'unique:'],
            "avatar" => ['sometimes', 'required', 'image', 'max:2000000'],
            "identification" => ['sometimes', 'required', 'image', 'max:2000000']
        ]);
    }
}
