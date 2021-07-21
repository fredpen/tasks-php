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
        'title', 'country_id',  'region_id',  'city_id',  'address', "linkedln", "bio",  'avatar'
    ],

    'userSecurityUpdate' => [
        'name',  'title',  'phone_number',  'country_id',  'region_id',  'city_id',  'address', "linkedln", "bio", "email", 'avatar', 'identification', 'security_answer'
    ],

    'canSkipBeforeApplying' => [
        'linkedln', 'security_answer'
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

    // numOfTaskMasters
    'numOfTaskMasters' =>  range(1, 10),

    // publishable params
    'canPublish' =>  [
        'task_id' => 'task id ',
        'sub_task_id' => 'sub task id ',
        'country_id' => 'country id ',
        'region_id' => 'region id ',
        // 'city_id' => 'city id ',
        'model' => 'Projet type(model) ',
        'num_of_taskMaster' => 'Number of task master ',
        'budget' => 'Budget ',
        'experience' => 'experience ',
        'proposed_start_date' => 'proposed start date ',
        'description' => 'description ',
        'title' => 'title ',
        'address' => 'address ',
        'duration' => 'duration '
    ],

    // searchQuery params using whereIn
    'whereInSearchQuery' =>  [
        "task_id",
        "sub_task_id",
        "country_id",
        "region_id",
        "city_id",
        "model",
        "experience"

    ]
];
