<?php

namespace App\Services;

use App\Write;
use App\Board;

class WriteSingleton
{
    public static function getInstance($boardId, $writeId, $id)
    {
        static $write;
        if (is_null($write) || $write[$id] != $writeId || $write->board_id != $boardId) {
            $writeModel = new Write();
            $board = Board::getBoard($boardId);
            $writeModel->setTableName($board->table_name);
            $write = $writeModel->find($writeId);
            $write->board_id = $board->id;
        }

        return $write;
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
