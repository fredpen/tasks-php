<?php

namespace App\Http\Controllers;

use App\ProjectAppliedUser;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectApplicationController extends Controller
{
    protected $projectAppliedUser;

    public function __construct(ProjectAppliedUser $projectAppliedUser)
    {
        $this->middleware(['auth:api', 'verified', 'isActive']);
        $this->projectAppliedUser = $projectAppliedUser;
    }


    public function apply(Request $request)
    {
        $validatedData = $this->validateApply($request->all());
        if ($validatedData->fails()) {
            return ResponseHelper::badRequest($validatedData->errors()->first());
        }
        $data = $request->only(['resume', 'project_id']);
        $data['user_id'] = Auth::id();
        $apply = $this->projectAppliedUser->firstOrCreate($data);
        return $apply ? ResponseHelper::sendSuccess($apply) : ResponseHelper::serverError();
    }

    public function withDrawApplication($projectId) 
    {
        $projectAppliedUser = $this->projectAppliedUser->where(['project_id' => $projectId, 'user_id' => Auth::id()])->first();
        if (! $projectAppliedUser) {
            return ResponseHelper::notFound();
        }
        $status = $projectAppliedUser->delete();
        return $status ? ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
    }
    
    public function projectApplications($projectId) 
    {
        $projectAppliedUser = $this->projectAppliedUser->where('project_id', $projectId)->with('applications')->get();
        return $projectAppliedUser->count() ? ResponseHelper::sendSuccess($projectAppliedUser) : ResponseHelper::notFound();
    }
    
    public function myApplications() 
    {
        $projectAppliedUser = $this->projectAppliedUser->where('user_id', Auth::id())->with('projects')->get();
        return $projectAppliedUser->count() ? ResponseHelper::sendSuccess($projectAppliedUser) : ResponseHelper::notFound();
    }

   
    private function validateApply($request)
    {
        return Validator::make($request, [
            'project_id' => 'required|integer',
            'resume' => 'required|string',
        ]);
    }
}
