<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use File;
use App\Models\BoardFile;

class BoardsEventListener
{
    public $writeModel;
    public $boardModel;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->writeModel = app()->tagged('write')[0];
        $this->boardModel = app()->tagged('board')[0];
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        // 다운로드전 다운로드수 증가, 포인트 계산하는 이벤트
        $events->listen(
            \App\Events\BeforeDownload::class,
            __CLASS__. '@beforeDownload'
        );
        // 글 읽기전 조회수 증가, 포인트 계산하는 이벤트
        $events->listen(
            \App\Events\BeforeRead::class,
            __CLASS__. '@beforeRead'
        );
        // RSS 보기전 검사하는 이벤트
        $events->listen(
            \App\Events\GetRssView::class,
            __CLASS__. '@getRssView'
        );
        // 답변글 쓸 수 있는지 검사하는 이벤트
        $events->listen(
            \App\Events\WriteReply::class,
            __CLASS__. '@writeReply'
        );
    }

    /**
     * 다운로드전 다운로드수 증가, 포인트 계산하는 이벤트
     *
     * @param \App\Events\BeforeDownload $event
     */
    public function beforeDownload(\App\Events\BeforeDownload $event)
    {
        if(!$event->file) {
            abort(500, '파일 정보를 찾을 수 없습니다.');
        }

        $filePath = storage_path('app/public/'. $event->board->table_name. '/'. $event->file->file);

        if(!File::exists($filePath)) {
            abort(500, $event->file->source. ' 파일을 찾을 수 없습니다.');
        }

        $user = auth()->user();
        $write = $event->write->find($event->file->write_id);
        $sessionName = 'session_download_'. $event->board->table_name. '_'. $write->id. '_'. $event->file->board_file_no;
        if( (auth()->check() && session()->get('admin')) || ($user && $user->id == $write->user_id)) {   // 관리자나 작성자 본인이면 패스

        } else if(!session()->get($sessionName)) { // 사용자의 다운로드 세션이 존재하지 않는다면
            // 포인트 차감
            $event->write->calculatePoint($write, $event->request, 'download');

            // 다운로드 횟수 증가
            BoardFile::where([
                'board_id' => $event->file->board_id,
                'write_id' => $write->id,
                'board_file_no' => $event->file->board_file_no,
            ])->increment('download', 1);

            session()->put($sessionName, true);
        }
    }

    /**
     * 글 읽기전 조회수 증가, 포인트 계산하는 이벤트
     *
     * @param \App\Events\BeforeRead $event
     */
    public function beforeRead(\App\Events\BeforeRead $event)
    {
        $writeModel = $event->write;
        $writeModel->setTableName($event->board->table_name);
        $writeModel->board = $event->board;
        $write = $event->write;

        $hit = $write->hit;
        $user = auth()->user();
        $userId = !$user ? 0 : $user->id;
        $userHash = !$user ? '' : $user->id_hashkey;
        $sessionName = "session_view_". $writeModel->getTable(). '_'. $write->id. '_'. $userHash;
        if(!session()->get($sessionName) && $userId != $write->user_id) {
            // 조회수 증가
            $hit = $writeModel->increaseHit($writeModel, $write);
            // 포인트 계산(차감)
            $writeModel->calculatePoint($write, $event->request, 'read');

            session()->put($sessionName, true);
        }
    }

    /**
     * RSS 보기전 검사하는 이벤트
     *
     * @param \App\Events\GetRssView $event
     */
    public function getRssView(\App\Events\GetRssView $event)
    {
        if($event->board->read_level >= 2) {
            abort(500, '비회원 읽기가 가능한 게시판만 RSS 지원합니다.');
        }
        if(!$event->board->use_rss_view) {
            abort(500, 'RSS 보기가 금지되어 있습니다.');
        }
    }

    /**
     * 답변글 쓸 수 있는지 검사하는 이벤트
     *
     * @param \App\Events\WriteReply $event
     */
    public function writeReply(\App\Events\WriteReply $event)
    {
        $request = $event->request;
        if(strpos($request->getRequestUri(), 'reply')) {	// 글 답변일 경우
            $boardName = $event->boardName;
            $writeId = $event->writeId;
            $user = auth()->user();
            $level = auth()->check() ? $user->level : 1;
            $boardModel = app()->tagged('board')[0];
            $board = $boardModel::getBoard($boardName, 'table_name');
            $notices = explode(',', $board->notice);

            $write = $this->writeModel::getWrite($board->id, $writeId);
            if(str_contains($write->option, 'secret')) {
                if(auth()->check() && !$user->isBoardAdmin($board) && $user->id != $write->user_id) {
                    abort(500, '비밀글에는 자신 또는 관리자만 답변이 가능합니다.');
                }
            }
            if (in_array((int)$writeId, $notices)) {
               abort(500,'공지에는 답변 할 수 없습니다.');
            } else if ($write && strlen($write->reply) == 10) { // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
               abort(500,'더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.');
            }

        }
    }
}
