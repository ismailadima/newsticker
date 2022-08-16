<?php

namespace App\Interfaces;

use App\Http\Library\Serializer;

// use App\Http\Requests\UserRequest;

interface RunningTextInewsInterface
{
    public function storeRunningtext($request);
    public function updateRunningText($newsticker, $request);
    public function updateLineRunningText($newsticker, $contents1, $contents2, $request);
    public function deleteLineRunningText($newsticker, $request);
    public function publishRunningText();
    public function joinAllCategoryInewsContent();
    public function buildFileText();

}