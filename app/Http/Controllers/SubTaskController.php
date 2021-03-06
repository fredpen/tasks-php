<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\SubTask;

class SubTaskController extends Controller
{
    protected $subTask;

    public function __construct(SubTask $subTask)
    {
        $this->subTask = $subTask;
    }

    public function index()
    {
        return $this->paginateMe($this->subTask);
    }


    public function store(Request $request)
    {
        $request->validate([
            'task_id' => ['required', 'exists:tasks,id'],
            'name' => ['required', 'unique:sub_tasks', 'min:3']
        ]);

        $subTask = $this->subTask
            ->create($request->only(['name', 'task_id']));

        return $subTask ?
            ResponseHelper::sendSuccess() :
            ResponseHelper::serverError();
    }

    public function show($subTaskId)
    {
        $subTask =  $this->subTask->where('id', $subTaskId)->first();
        return $subTask ?
            ResponseHelper::sendSuccess($subTask) :
            ResponseHelper::notFound();
    }

    public function update(Request $request, $subTaskId)
    {
        $request->validate([
            'task_id' => ['required', 'exists:tasks,id'],
            'name' => ['required', 'unique:sub_tasks', 'min:3']
        ]);

        $subTask =  $this->subTask->where('id', $subTaskId);
        if (!$subTask->count()) {
            return ResponseHelper::notFound();
        }

        $subTask = $subTask->update($request->only(['name', 'task_id']));
        return $subTask ?
            ResponseHelper::sendSuccess() :
            ResponseHelper::serverError();
    }


    public function delete($subTaskId)
    {
        $subTask =  $this->subTask->where('id', $subTaskId);
        if (!$subTask->count()) {
            return ResponseHelper::notFound();
        }

        $deleteSubTask = $subTask->delete();
        return $deleteSubTask ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::serverError();
    }
}
