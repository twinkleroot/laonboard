<?php

if(! function_exists('isShowCaptchaFromWriteCount')) {
    function isShowCaptchaFromWriteCount($id)
    {
        $count = App\Models\BoardNew::whereUserId($id)->whereDate('created_at', '=', Carbon\Carbon::today()->toDateString())->count();

        if($count > config('laon.todayWriteCount')) {
            return true;
        }

        return false;
    }
}
