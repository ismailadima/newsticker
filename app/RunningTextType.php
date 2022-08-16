<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RunningTextType extends Model
{
    //List type rt
    const TYPE_REGULAR_PROGRAM = 1;
    const TYPE_SPECIAL_PROGRAM = 2;

    //List Type Rt string
    const TYPE_REGULAR_PROGRAM_STR = "Regular Program";
    const TYPE_SPECIAL_PROGRAM_STR = "Special Program";

    protected $table = 'running_text_type';
}
