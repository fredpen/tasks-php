<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function search(Request $request)
    {
        $this->validateSearch($request);
        $users = User::query();

        if ($request->searchTerm) {
            $searchTerm = $request->searchTerm;
            $users = $users->where(function ($query) use ($searchTerm) {
                $query->where('name', "like", "%{$searchTerm}%")
                    ->orWhere('email', "like", "%{$searchTerm}%")
                    ->orWhere('address', "like", "%{$searchTerm}%")
                    ->orWhere('phone_number', "like", "%{$searchTerm}%");
            });
        }


        if ($request->country_ids) {
            $users->whereIn('country_id', $request->country_ids);
        }

        if ($request->region_ids) {
            $users->whereIn('region_id', $request->region_ids);
        }

        if ($request->city_ids) {
            $users->whereIn('city_id', $request->city_ids);
        }

        if (!$users->count()) {
            return ResponseHelper::successNoContent("Query returns empty");
        }

        return ResponseHelper::sendSuccess(
            $users
            ->with(['country:id,name', 'region:id,name', 'city:id,name'])
            ->latest()
            ->paginate($this->limit)
        );
    }

    // public function update(string $user_id)
    // {
    //     $user = User::find($user_id);
    //     if (!$user) {
    //         return ResponseHelper::badRequest("Invalid user ID");
    //     }

    //     return $user->delete() ?
    //         ResponseHelper::successNoContent([]) : ResponseHelper::serverError("couldnt delete user");
    // }

    public function delete(string $user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return ResponseHelper::badRequest("Invalid user ID");
        }

        return $user->delete() ?
            ResponseHelper::successNoContent([]) : ResponseHelper::serverError("couldnt delete user");
    }

    public function all()
    {
        $users = User::query();

        return $users->count() ?
            ResponseHelper::sendSuccess($users
                ->with(['country:id,name', 'region:id,name', 'city:id,name'])
                ->latest()
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }

    private function validateSearch(Request $request)
    {
        return $request->validate([
            'searchTerm' => 'sometimes|string|min:4',
            'country_ids' => 'sometimes|array|min:1',
            'region_ids' => 'sometimes|array|min:1',
            'city_ids' => 'sometimes|array|min:1',
        ]);
    }
}
