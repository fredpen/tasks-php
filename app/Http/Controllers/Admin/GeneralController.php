<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Region;
use App\Models\Tasks;
use App\Models\User;

class GeneralController extends Controller
{
    public function landing()
    {
        return ResponseHelper::sendSuccess(
            [
                "popularCategories" => Tasks::with('subTasks')->withCount('projects')->orderBy('projects_count', 'desc')->take(8)->get(),
                "featuredJobs" => (new Project)->appliable()->latest()->limit(10)->get(),
                "featuredCities" => Region::withCount('projects')->orderBy('projects_count', 'desc')->take(4)->get(),
                "topFreelancers" => User::withCount('myApplications')->orderBy('my_applications_count', 'desc')->take(4)->get()
            ],
            'successful'
        );
    }
}
