<?php

namespace App\Http\Controllers;

use App\Tasks;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     
        $this->middleware(['auth', 'verified', 'isActive'])->only('welcome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tasks = Tasks::with('subtasks:id,name,task_id')->get();
        return view('home', compact('tasks'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        
        return view('welcome');
    }
}
