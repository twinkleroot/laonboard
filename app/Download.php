<?php

namespace App;

class Download
{
    // 다운로드시 처리할 내용
    public function beforeDownload($request, $writeModel, $boardName, $writeId, $fileNo)
    {
        $board = $writeModel->board;

        $file = BoardFile::where([
            'board_id' => $board->id,
            'write_id' => $writeId,
            'board_file_no' => $fileNo,
        ])->first();

        $user = auth()->user();
        $write = $writeModel->find($writeId);
        $sessionName = 'session_download_'. $board->table_name. '_'. $write->id. '_'. $fileNo;
        if( (auth()->check() && session()->get('admin')) || ($user && $user->id == $write->user_id)) {   // 관리자나 작성자 본인이면 패스
            
        } else if(!session()->get($sessionName)) { // 사용자의 다운로드 세션이 존재하지 않는다면
            // 포인트 차감
            $writeModel->calculatePoint($write, $request, 'download');

            // 다운로드 횟수 증가
            $file->where([
                'board_id' => $board->id,
                'write_id' => $writeId,
                'board_file_no' => $fileNo,
            ])->update(['download' => $file->download + 1]);

            session()->put($sessionName, true);
        }

        return $file;
    }
}
