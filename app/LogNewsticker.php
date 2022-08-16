<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class LogNewsticker extends Model
{
    protected $table = 'log_newstickers';
    protected $guarded = [];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $primaryKey = 'id';

    //STATUS PERUBAHAN LOG
    const STATUS_CREATE = 'create';
    const STATUS_UPDATE = 'update';
    const STATUS_UPDATE_LINE = 'update-line';
    const STATUS_UPDATE_UPLOAD = 'update-upload';
    const STATUS_PUBLISH = 'publish';
    const STATUS_PUBLISH_AUTO = 'publish-auto';
    const STATUS_UNPUBLISH = 'unpublish';
    const STATUS_DELETE_LINE = 'delete-line';
    const STATUS_UPDATE_BY_AUTODELETE = 'update-by-autodelete';
    const STATUS_PUBLISH_BY_AUTODELETE = 'publish-by-autodelete';
    const STATUS_DELETED_LINE_BY_AUTODELETE = 'deleted-line-by-autodelete';

    public function newsticker()
    {
        return $this->belongsTo(Newsticker::class, 'newstickers_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
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
