<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\Board;
use App\Models\Write;
use App\Models\Comment;

class ThemePreviewsController extends Controller
{
    public $theme;
    public $writeModel;
    public $comment;

    public function __construct(Theme $theme, Comment $comment, Write $write)
    {
        $this->theme = $theme;
        $this->comment = $comment;
        $this->writeModel = $write;
        $this->writeModel->board = Board::first();
        $this->writeModel->setTableName($this->writeModel->board->table_name);
    }

    // 커뮤니티 메인 미리 보기
    public function index($themeName)
    {
        // 미리 보기 데이터
        $preview = $this->theme->getPreview('index', $themeName);
        // 파라미터 배열 결합
        $params = $preview;

        return view('admin.themes.preview', $params);
    }

    // 게시판 글 목록 미리 보기
    public function boardList($themeName, Request $request)
    {
        // 미리 보기 데이터
        $preview = $this->theme->getPreview('boardList', $themeName);
        // 글 목록 데이터
        $list = $this->writeModel->getIndexParams($this->writeModel, $request);
        // 파라미터 배열 결합
        $params = array_collapse([$preview, $list, ['skin' => $list['board']->skin] ]);

        return view('admin.themes.preview', $params);
    }

    // 게시판 글 보기 미리 보기
    public function boardView($themeName, Request $request)
    {
        $boardId = $this->writeModel->board->id;
        $writeId = $this->writeModel->where('is_comment', 0)->orderBy('id', 'desc')->first()->id;
        // 미리 보기 데이터
        $preview = $this->theme->getPreview('boardView', $themeName);
        // 게시글 데이터
        $view = $this->writeModel->getViewParams($this->writeModel, $writeId, $request);
        // 댓글 데이터
        $comments = $this->comment->getCommentsParams($this->writeModel, $writeId, $request);
        // 이전글, 다음글 데이터 추가
        $prevAndNext = $this->writeModel->getPrevNextView($this->writeModel, $writeId, $request);
        // 파라미터 배열 결합
        $params = array_collapse([$preview, $view, $comments, $prevAndNext, ['skin' => $view['board']->skin, 'currenctCategory' => ''] ]);

        return view('admin.themes.preview', $params);
    }
}
