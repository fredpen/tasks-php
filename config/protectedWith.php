<?php

return [
    //   project
    'project' => ['task:name,id', 'subtask:name,id', 'owner:name,id,orders_out,orders_in,ratings,ratings_count', 'country:name,id', 'region:name,id', 'city:name,id', 'photos:url,project_id'],

    //   project
    'favouredProject' => ['project.task:name,id', 'project.subtask:name,id', 'project.owner:name,id', 'project.photos:url,project_id', 'project.region:name,id', 'project.city:name,id']
];
