<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //List Categories
    const CAT_INFOTAINMENT = 1;
    const CAT_NEWS = 2;
    const CAT_PROMO = 3;
    const CAT_ALL = 4;
    const CAT_BREAKING_NEWS = 5;
    const CAT_SERGAP_NEWS = 6;

    // protected $table = 'categories';
}
