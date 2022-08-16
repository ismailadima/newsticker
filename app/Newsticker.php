<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Newsticker extends Model
{
    //STATUS PUBLISH
    const STATUS_PUBLISH = 1;
    const STATUS_UNPUBLISH = 2;
    const STATUS_NONAKTIF = 3;

    //MAX CHARACTER
    const MAX_CHARACTER_CONTENT = 2000;
    
    protected $table = 'newstickers';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function logs()
    {
        return $this->hasMany(LogNewsticker::class, 'newstickers_id', 'id');
    }

    public function deletedTime()
    {
        return $this->hasMany(DeleteSchedule::class, 'newsticker_id', 'id');
    }

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
