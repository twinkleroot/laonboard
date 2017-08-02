<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Write;
use App\Board;
use App\Download;

class DownloadController extends Controller
{
    public $download;
    public $writeModel;

    public function __construct(Download $download, Request $request, Write $write)
    {
        $this->download = $download;
        $this->writeModel = $write;
        $this->writeModel->board = Board::getBoard($request->boardId);
        $this->writeModel->setTableName($this->writeModel->board->table_name);
    }

    // 글 보기 중 첨부파일 다운로드
    public function download(Request $request, $boardId, $writeId, $fileNo)
    {
        $file = $this->download->beforeDownload($request, $this->writeModel, $boardId, $writeId, $fileNo);

        $filePath = storage_path('app/public/'.$this->writeModel->board->table_name. '/'. $file->file);

        return response()->download($filePath, $file->source);
    }
}
