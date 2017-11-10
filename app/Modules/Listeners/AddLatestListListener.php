<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddLatestList;

class AddLatestListListener
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
     * @param  AddLatestList  $event
     * @return void
     */
    public function handle(AddLatestList $event)
    {
        $board = app()->tagged('board')[0];
        $query =
            $board->select('boards.*', 'groups.subject as group_subject', 'groups.order as group_order')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->orderBy('groups.order')
            ->orderBy('boards.order');

        if(!session()->get('admin')) {
            $query = $query->where('boards.use_cert', 'not-use');
        }
        $boards = $query->get();

        $latests = getLatestWrites($boards, 5, 25);

        $params = [
            'latests' => $latests
        ];

        $theme = cache('config.theme')->name ? : 'default';

        echo viewDefault("$theme.latests.default.index", $params);
    }
}
