<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Auth;

class User extends Authenticatable
{
    protected $guarded = [];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $primaryKey = 'id';

    //USER STATUS
    const IS_ADMIN = "Y";
    const IS_NOT_ADMIN = "N";

    //MCR OR NOT
    const IS_MCR = "Y";
    const IS_NOT_MCR = "N";

    //USER STATUS
    const USER_ACTIVE = "Y";
    const USER_NON_ACTIVE = "N";

    //USER STATUS ARRAY
    public static $statusses = [
        "Y" => "Active",
        "N" => "Non Active"
    ];

    
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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
