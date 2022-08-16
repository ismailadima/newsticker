<?php

namespace App\Traits;

use App\LogAuthUser;
use App\Unit;
use App\User;
use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public function createUserAuthLogs($type = null)
    {
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $is_mobile = is_numeric(strpos($browser, "mobile"));
        $platform = ($is_mobile) ? "mobile" : "desktop";
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $log = new LogAuthUser();
        $log->browser = $browser;
        $log->ip_address = $ip_address;
        $log->platform = $platform;
        $log->type = $type;
        $log->save();
    }

    public function isMCR()
    {
        $status = false;
        if(Auth::user()->is_mcr == User::IS_MCR){
            $status= true;
        }
        return $status;
    }

    public function isUserInews()
    {
        $status = false;
        if(Auth::user()->unit_id == Unit::UNIT_INEWS){
            $status= true;
        }
        return $status;
    }
}