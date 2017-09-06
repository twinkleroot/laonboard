<?php

namespace App\Http\Controllers;

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
        $this->writeModel->board = Board::getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
    }

    // 글 보기 중 첨부파일 다운로드
    public function download(Request $request, $boardName, $writeId, $fileNo)
    {
        $file = $this->download->beforeDownload($request, $this->writeModel, $boardName, $writeId, $fileNo);

        $filePath = storage_path('app/public/'.$boardName. '/'. $file->file);

        return response()->download($filePath, $file->source);
    }
}
