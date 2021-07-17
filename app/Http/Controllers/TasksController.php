<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Tasks;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    protected $task;

    public function __construct(Tasks $task)
    {
        $this->task = $task;
    }

    public function taskWithSubTasks()
    {
        $tasks =  $this->task->with("subTasks:id,task_id,name");
        return $tasks->count() ? ResponseHelper::sendSuccess($tasks->get()) : ResponseHelper::notFound();
    }

    public function index()
    {
        $tasks =  $this->task->all();
        return $tasks->count() ? ResponseHelper::sendSuccess($tasks) : ResponseHelper::notFound();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:tasks'
        ]);

        $task = $this->task->create($request->only('name'));
        return $task ? ResponseHelper::sendSuccess([], "Task created successfully") : ResponseHelper::serverError();
    }

    public function show($taskId)
    {
        $task =  $this->task->where('id', $taskId);
        if (!$task) {
            return ResponseHelper::notFound();
        }

        $task =  $task->with("subTasks")->first();
        return ResponseHelper::sendSuccess($task);
    }

    public function update(Request $request, $taskId)
    {
        $request->validate([
            'name' => 'required|min:3|unique:tasks'
        ]);

        $task =  $this->task->where('id', $taskId);
        if (!$task) {
            return ResponseHelper::notFound();
        }

        $task = $task->update($request->only('name'));
        return ResponseHelper::sendSuccess([], "Task updated successfully");
    }

    public function delete($taskId)
    {
        $task =  $this->task->where('id', $taskId);
        if (!$task->count()) {
            return ResponseHelper::notFound();
        }

        $task->first()->subTasks()->delete();
        $task->delete();
        return ResponseHelper::sendSuccess([]);
    }
}
