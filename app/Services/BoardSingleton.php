<?php

namespace App\Services;

use App\Board;

class BoardSingleton
{
    public static function getInstance($boardId)
    {
        static $board;
        if (is_null($board) || $board->id != $boardId) {
            $board = Board::find($boardId);
        }

        return $board;
    }

    /**
     * 이 클래스는 싱글턴으로 사용할 것이므로 이 클래스 외부에서 생성하는 것을 금지하기 위해 생성자를 protected 로 제한한다.
     */
    protected function __construct()
    {
    }

    /**
     * 싱글턴 인스턴스를 복제할 수 없도록 복제 메소드를 private으로 제한한다.
     */
    private function __clone()
    {
    }

    /**
     * 싱글턴 인스턴스를 unserialize 하지 못하게 private 으로 제한한다.
     */
    private function __wakeup()
    {
    }
}
