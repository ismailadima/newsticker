<?php

namespace App\Interfaces;

use App\Http\Library\Serializer;

// use App\Http\Requests\UserRequest;

interface RunningTextCommandInterface   
{
    //For Command Shechuling
    public function autoPublishContentToday($unit);
    public function autoDeleteLineToday();
}