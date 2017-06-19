<?php

namespace App;

use App\Board;
use App\Menu;
use DB;
use Cache;

class Main
{
    public function getMainContents($skin, $group='')
    {
        $query = Board::selectRaw('boards.*, groups.id as group_id, groups.subject as group_subject, groups.order as group_order')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->where('boards.device', '<>', 'mobile')
            ->where('boards.use_cert', 'not-use');

        // 게시판 그룹 페이지를 보여줄 때
        if($group) {
            $query->where('groups.id', $group);
        }

        $boards = $query->orderBy('groups.order')->orderBy('boards.order')->get();
        $latestList = $this->getLatestWrites($skin, $boards, 5, 25);

        return [
            'latestList' => $latestList,
            'latestSkin' => $skin,
        ];
    }

    private function getLatestWrites($skin, $boards, $pageRows, $titleLength)
    {
        $latestList = array();
        $cacheMinutes = 60;
        $i = 0;
        foreach($boards as $board) {
            $latestList[$i] = Cache::remember('main-'. $board->table_name, $cacheMinutes, function() use($board, $pageRows) {
                return DB::table('write_'. $board->table_name)
                        ->where('is_comment', 0)
                        ->orderBy('num')
                        ->limit($pageRows)
                        ->get();
            });
            $latestList[$i]->board_id = $board->id;
            $latestList[$i]->board_subject = $board->subject;
            $i++;
        }

        return $latestList;
    }
}
