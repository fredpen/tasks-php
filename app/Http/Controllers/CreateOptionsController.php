<?php

namespace App\Http\Controllers;

use App\Helpers\FileRetrieverHelper;

class CreateOptionsController extends Controller
{
    public function createOPtions()
    {
        return FileRetrieverHelper::fetchCreateOptions();
    }
    
    public function roles()
    {
        return FileRetrieverHelper::roles();
    }
}
