<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Region;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Support\Facades\Config;

class GeneralController extends Controller
{
    public function landing()
    {
        return ResponseHelper::sendSuccess(
            [
                "popularCategories" => Tasks::with('subTasks')->withCount('projects')->orderBy('projects_count', 'desc')->take(8)->get(),
                "totalJobs" => Project::count(),
                "totalUsers" => User::count(),
                "featuredJobs" => (new Project)->appliable()->with(Config::get('protectedWith.project'))->latest()->limit(10)->get(),
                "featuredCities" => Region::withCount('projects')->orderBy('projects_count', 'desc')->take(4)->get(),
                "topFreelancers" => User::with(['country', "city", "region"])->withCount('myApplications')->orderBy('my_applications_count', 'desc')->take(6)->get()
            ],
            'successful'
        );
    }
}
