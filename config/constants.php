<?php

return [
    // roles
    'roles' => [
        // 0 => 'Admin',
        '01' => 'Task Giver',
        '02' => 'Task Master',
    ],

    // projectModel
    'projectModels' => [
        '01' => 'remote',
        '02' => 'on-site',
    ],

    // allowed upload
    'userUpdate' => [
        'name',  'title',  'phone_number',  'country_id',  'region_id',  'city_id',  'address', "imageurl", "linkedln", "bio", "email", "password"
    ],

    // status
    'projectStatus' => [
        '0' => 'Draft',
        '1' => 'posted',
        '2' => 'started',
        '3' => 'cancelled',
        '4' => 'completed',
        '5' => 'deleted',

    ],

    // project duration
    'projectDuration' =>  [
        'Few Hours' => 'Few Hours',
        'A day' => 'A day',
        'less than a week' => 'less than a week',
        'less than a month' => 'less than a month',
        'less than three months' => 'less than three months',
        'more than three months' => 'more than three months',
        'not sure' => 'not sure'
    ],

    // level of expertise
    'projectExpertise' =>  [
        '1' => 'Junior',
        '2' => 'Average',
        '3' => 'Experienced',
        '4' => 'Expert',
        '5' => 'Veteran'
    ],

    // publishable params
    'canPublish' =>  [
        'task_id' => 'task id is missing',
        'sub_task_id' => 'sub task id is missing',
        'country_id' => 'country id is missing',
        'region_id' => 'region id is missing',
        'city_id' => 'city id is missing',
        'model' => 'Projet type(model) is missing',
        'num_of_taskMaster' => 'Number of task master is missing',
        'budget' => 'Budget is missing',
        'experience' => 'experience is missing',
        'proposed_start_date' => 'proposed start date is missing',
        'description' => 'description is missing',
        'title' => 'title is missing',
        'address' => 'address is missing',
        'duration' => 'duration is missing'
    ]
];
