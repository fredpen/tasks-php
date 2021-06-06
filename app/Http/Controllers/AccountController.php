<?php

namespace App\Http\Controllers;

use App\Country;
use App\Jobs\MarkAllNotificationAsRead;
use App\Tasks;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() 
    {
        $this->middleware(['auth:api', 'verified']);
        $this->middleware(['isActive'])->only('show');

    }

    private function validateTaskMaster($request)
    {
        return $request->validate(
            [
                'country_id' => 'required',
                "region_id" => 'required',
                'city_id' => 'required',
                'address' => 'required',
                'title' => 'required',
                'name' => 'required',
                'bio' => 'required',
                'address' => 'required',
                'linkedln' => 'min:0'
            ]
        );
    }

    private function validateTaskGiver($request)
    {
        return $request->validate(
            array(
                'country_id' => 'required',
                "region_id" => 'required',
                'city_id' => 'required',
                'address' => 'required',
                'name' => 'required',
                'linkedln' => 'min:0'
            )
        );
    }

    public function show(User $account)
    {
        if ($account->isTaskGiver()) {
            return view('taskGiver.show', [
                'user' => $account,
                'appliedProjects' => $account->projects,
                'assignedProjects' => []
            ]);
        }

        return view('taskMaster.show', [
            'user' => $account,
            'skill_ids' =>  $account->fetchskillsId(),
            'appliedProjects' => $account->assignedProjects->take(5),
            'assignedProjects' => $account->appliedProjects->take(5)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::id() != $id) abort('403'); // policy check
        $user = User::findOrFail($id);
        $countries = Country::get(['name', 'id']);
        if ($user->isTaskGiver()) return view('taskGiver.edit', compact('user', 'countries'));

        $skill_ids = $user->fetchskillsId();
        $jobsId = $user->fetchJobsId();
        $tasks = Tasks::get(['id', 'name']);
        return view('taskMaster.edit', compact('user', 'tasks', 'skill_ids', 'countries', 'jobsId'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::id() != $id) abort('403'); //policy check
        $user = User::findOrFail($id);
        $validatedData = ($user->role_id == 1) ? $this->validateTaskGiver($request) : $this->validateTaskMaster($request); //validate request inputs
        $validatedData['isActive'] = 1;
        $user->update($validatedData);

        if ($request->skills) $user->skills()->sync($request->skills);  //update user skills
        if ($request->jobs) $user->jobs()->sync($request->jobs);  //update user jobs
        return redirect()->action('AccountController@show', $id)->with("message", "Profile updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function notifications()
    {
        MarkAllNotificationAsRead::dispatch(Auth::user());
        return view('notifications', ['allNotifications' =>  Auth::user()->notifications]);
    }
    /** show 
     * show alist of all my tasks
     * @return collection of tasks
     */

    public function myTasks()
    {
        return view('taskMaster.myTask', [
            'assignedProjects' => Auth::user()->assignedProjects,
            'appliedProjects' => Auth::user()->appliedProjects
        ]);
    }
}
