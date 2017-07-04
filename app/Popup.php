<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Common\Util;

class Popup extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'popups';

    public function getPopupData()
    {
        $popups = Popup::where('begin_time', '<=', Carbon::now())
            ->where('end_time', '>', Carbon::now())
            ->get();

        foreach($popups as $popup) {
            $popup->content = Util::convertContent($popup->content, 1);
        }

        return $popups;
    }
}
