<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCreateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        $todayDate = date('d/m/Y');

        return [
            'task_id' => 'required|integer|exists:tasks,id',
            'sub_task_id' => 'required|integer|exists:sub_tasks,id',
            'country_id' => 'required_if:model,2|exclude_if:model,1|integer|exists:countries,id',
            'region_id' => 'required_if:model,2|exclude_if:model,1|integer|exists:regions,id',
            'city_id' => 'required_if:model,2|exclude_if:model,1|integer|exists:cities,id',

            'model' => 'required|integer|min:1|max:2',
            // 'num_of_taskMaster' => 'required|integer|min:1|max:10',
            'budget' => 'required|numeric|min:1000',
            'experience' => 'required|integer|min:1|max:5',
            'proposed_start_date' =>  "required|date_format:Y-m-d|after_or_equal:'.$todayDate'",
            'description' => 'required|string|min:20',
            'title' => 'required|string|min:10',
            'duration' => 'required|string',
            'address' => 'required_if:model,2|exclude_if:model,1|string|min:10',
        ];
    }
}
