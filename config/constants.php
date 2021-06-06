<?php

return [
    // roles
    'roles' => [
        // 0 => 'Admin',
        '01' => 'Task Giver',
        '02' => 'Task Master',
    ],

    // allowed upload
    'userUpdate' => [
        'name',  'title',  'phone_number',  'country_id',  'region_id',  'city_id',  'address', "imageurl", "linkedln", "bio", "email", "password"
    ],

    // meta data
    'metaData' =>  [
        array('metaname' => 'color', 'metavalue' => 'blue'),
        array('metaname' => 'size', 'metavalue' => 'big')
    ],

    // status
    'status' => [
        '0' => 'created',
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
    'expertise' =>  [
        'Beginner' => 'Average',
        'Experienced' => 'Experienced',
        'Expert' => 'Expert',
        'veteran' => 'Veteran'
    ]




];
