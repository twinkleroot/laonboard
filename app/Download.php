<?php

namespace App;

use App\BoardFile;

class Download
{
    // 다운로드시 처리할 내용
    public function beforeDownload($request, $writeModel, $boardId, $writeId, $fileNo)
    {
        $file = BoardFile::where([
            'board_id' => $boardId,
            'write_id' => $writeId,
            'board_file_no' => $fileNo,
            ])->first();

        $user = auth()->user();
        $board = $writeModel->board;
        $write = $writeModel->find($writeId);
        $sessionName = 'session_download_'. $board->table_name. '_'. $write->id. '_'. $fileNo;
        if(session()->get('admin') || $user->id == $write->user_id) {   // 관리자나 작성자 본인이면 패스
            ;
        } else if(!session()->get($sessionName)) { // 사용자의 다운로드 세션이 존재하지 않는다면
            // 포인트 차감
            $message = $writeModel->calculatePoint($write, $request, 'download');

            // 포인트 관련 에러 메세지가 있으면 출력함
            if($message != '') {
                return [ 'message' => $message ];
            }

            // 다운로드 횟수 증가
            $file->where([
                'board_id' => $boardId,
                'write_id' => $writeId,
                'board_file_no' => $fileNo,
            ])->update(['download' => $file->download + 1]);

            session()->put($sessionName, true);
        }

        return $file;
    }
}
