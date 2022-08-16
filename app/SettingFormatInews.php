<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class SettingFormatInews extends Model
{
    protected $table = 'setting_format_inews';
    protected $guarded = [];
    protected $primaryKey = 'id';

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    
}
