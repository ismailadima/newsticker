<?php

namespace App\Interfaces;

use App\Http\Library\Serializer;

// use App\Http\Requests\UserRequest;

interface RunningTextInterface
{
    public function storeRunningtext($request, $is_special_program);
    public function updateRunningText($newsticker, $request, $is_special_program, $is_have_deleted_sch);
    public function updateLineRunningText($newsticker, $contents1, $contents2, $request);
    public function deleteLineRunningText($newsticker, $request);
    public function publishRunningText($newsticker);
    public function validatedCountContent($request);
    
}