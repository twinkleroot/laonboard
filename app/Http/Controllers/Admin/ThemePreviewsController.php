<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\Main;
use App\Models\Board;
use App\Models\Write;
use App\Models\Comment;

class ThemePreviewsController extends Controller
{
    public $theme;
    public $main;
    public $writeModel;
    public $comment;

    public function __construct(Theme $theme, Main $main, Comment $comment, Write $write)
    {
        $this->theme = $theme;
        $this->main = $main;
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
        // 메인 최신글 데이터
        $main = $this->main->getMainContents($themeName, 'default');
        // 파라미터 배열 결합
        $params = array_collapse([$preview, $main]);

        return view('admin.themes.preview', $params);
    }

    // 게시판 글 목록 미리 보기
    public function boardList($themeName, Request $request)
    {
        // 미리 보기 데이터
        $preview = $this->theme->getPreview('boardList', $themeName);
        // 글 목록 데이터
        $list = $this->writeModel->getIndexParams($this->writeModel, $request);
        // 테마명 : 테마명에 해당하는 스킨이 존재하지 않으면 기본으로 설정
        $themeName = view()->exists("board.$themeName.index") ? $themeName : 'default';
        // 파라미터 배열 결합
        $params = array_collapse([$preview, $list, ['themeName' => $themeName] ]);

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
        // 테마명 : 테마명에 해당하는 스킨이 존재하지 않으면 기본으로 설정
        $themeName = view()->exists("board.$themeName.view") ? $themeName : 'default';
        // 파라미터 배열 결합
        $params = array_collapse([$preview, $view, $comments, $prevAndNext, ['themeName' => $themeName, 'currenctCategory' => ''] ]);

        return view('admin.themes.preview', $params);
    }
}
