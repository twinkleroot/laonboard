<?php

namespace App\Listeners;

use App\Events\BeforeDownload;
use App\BoardFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use File;

class BeforeDownloadListener
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
     * @param  BeforeDownload  $event
     * @return void
     */
    public function handle(BeforeDownload $event)
    {
        if(!$event->file) {
            abort(500, '파일 정보를 찾을 수 없습니다.');
        }

        $filePath = storage_path('app/public/'. $event->board->table_name. '/'. $event->file->file);

        if(!File::exists($filePath)) {
            abort(500, $event->file->source. ' 파일을 찾을 수 없습니다.');
        }

        $user = auth()->user();
        $write = $event->writeModel->find($event->file->write_id);
        $sessionName = 'session_download_'. $event->board->table_name. '_'. $write->id. '_'. $event->file->board_file_no;
        if( (auth()->check() && session()->get('admin')) || ($user && $user->id == $write->user_id)) {   // 관리자나 작성자 본인이면 패스

        } else if(!session()->get($sessionName)) { // 사용자의 다운로드 세션이 존재하지 않는다면
            // 포인트 차감
            $event->writeModel->calculatePoint($write, $event->request, 'download');

            // 다운로드 횟수 증가
            BoardFile::where([
                'board_id' => $event->file->board_id,
                'write_id' => $write->id,
                'board_file_no' => $event->file->board_file_no,
            ])->increment('download', 1);

            session()->put($sessionName, true);
        }
    }
}
