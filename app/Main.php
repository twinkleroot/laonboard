<?php

namespace App;

use App\Board;
use App\Group;
use DB;
use Cache;

class Main
{
    public function getMainContents($skin, $default)
    {
        if(!$skin) {
            $skin = $default;
        }
        $boards = Board::selectRaw('boards.*, groups.id as group_id, groups.subject as group_subject, groups.order as group_order')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->where('boards.device', '<>', 'mobile')
            ->where('boards.use_cert', 'not-use')
            ->orderBy('groups.order')
            ->orderBy('boards.order')
            ->get();

        $latestList = $this->getLatestWrites($boards, 5, 25);

        return [
            'boardList' => $latestList,
            'skin' => $skin,
        ];
    }

    public function getGroupContents($groupId, $skin, $default)
    {
        if(!$skin) {
            $skin = $default;
        }
        $boards = Board::where([
                'group_id' => $groupId,
                'use_cert' => 'not-use',
            ])
            ->where('device', '<>', 'mobile')
            ->where('list_level', '<=' , auth()->guest() ? 1 : auth()->user()->level)   // guest는 회원 레벨 1
            ->orderBy('order')
            ->get();

        $groupList = $this->getLatestWrites($boards, 5, 70);
        $group = Group::find($groupId);

        return [
            'boardList' => $groupList,
            'skin' => $skin,
            'groupName' => is_null($group) ? '' : $group->subject,
        ];
    }

    // 최근 게시물
    private function getLatestWrites($boards, $pageRows, $titleLength)
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
