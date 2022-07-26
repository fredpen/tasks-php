<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $todayDate = date('Y-m-d');

        return [
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'sometimes|integer|exists:tasks,id',
            'sub_task_id' => 'sometimes|integer|exists:sub_tasks,id',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'region_id' => 'sometimes|integer|exists:regions,id',
            'city_id' => 'sometimes|integer|exists:cities,id',

            'model' => 'sometimes|integer|min:1|max:2',
            'num_of_taskMaster' => 'sometimes|integer|min:1|max:10',
            'budget' => 'sometimes|numeric|min:1000',
            'experience' => 'sometimes|integer|min:1|max:5',
            'proposed_start_date' =>  "sometimes|date_format:Y-m-d|after_or_equal:'.$todayDate'",
            'description' => 'sometimes|string',
            'title' => 'sometimes|string|min:10',
            'duration' => 'sometimes|string',
            'address' => 'sometimes|string|min:10',
        ];
    }
}
