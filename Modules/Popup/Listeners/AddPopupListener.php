<?php

namespace Modules\Popup\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Popup\Events\AddPopup;
use Modules\Popup\Models\Popup;
use Carbon\Carbon;
use Schema;

class AddPopupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Popup\Events\AddPopup  $event
     * @return void
     */
    public function handle(AddPopup $event)
    {
        if(Schema::hasTable('popups')) {
            $popups = Popup::where('begin_time', '<=', Carbon::now())
                ->where('end_time', '>', Carbon::now())
                ->get();
            foreach($popups as $popup) {
                $popup->content = convertContent($popup->content, 1);
            }

            $params = [
                'popups' => $popups
            ];

            echo view('modules.popup.index', $params);
        }
    }
}
