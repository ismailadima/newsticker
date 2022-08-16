<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class LogAuthUser extends Model
{
    protected $table = 'log_auth_user';
    protected $guarded = [];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $primaryKey = 'id';

    //Type Log
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->created_by = !empty(Auth::user()->id) ? Auth::user()->id : '';
            $model->created_at = date('Y-m-d H:i:s');
        });

        self::updating(function($model){
            $model->updated_by = !empty(Auth::user()->id) ? Auth::user()->id : '';
            $model->updated_at = date('Y-m-d H:i:s');
        });
    }

}
