<?php

if(! function_exists('todayWriteCount')) {
    function todayWriteCount($id)
    {
        return App\Models\BoardNew::whereUserId($id)->whereDate('created_at', '=', Carbon\Carbon::today()->toDateString())->count();
    }
}
