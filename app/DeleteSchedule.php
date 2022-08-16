<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeleteSchedule extends Model
{
    //STATUS DELETE
    const STATUS_DELETED = 'Y';
    const STATUS_UNDELETED = 'N';
    const CONTENT_TYPE_1 = 1;
    const CONTENT_TYPE_2 = 2;

    protected $table = 'delete_schedule';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guard = [];

    public function isNewstickerHaveTimeDeleted($id_newsticker = null)
    {
        $count_deleted = $this->where('newsticker_id', $id_newsticker)
            ->whereNotNull('time_deleted')
            ->count();

        return ($count_deleted > 0) ? true : false;
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_by = session('user_sess');
        });

        self::updating(function ($model) {
            $model->updated_by = session('user_sess');
        });
    }
}
