<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    private $limit = 20;

    public function all()
    {
        $users = User::query();

        return $users->count() ?
            ResponseHelper::sendSuccess($users
                ->orderBy('updated_at', 'desc')
                ->paginate($this->limit)) : ResponseHelper::notFound();
    }
}
