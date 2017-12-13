<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;
use App\Models\BoardFile;

class BoardFilesController extends Controller
{
    public $boardFileModel;

    public function __construct(Request $request, WriteInterface $write, BoardInterface $board, BoardFile $boardFile)
    {
        $this->writeModel = $write;
        $this->writeModel->board = $board->getBoard($request->boardName, 'table_name');
        $this->writeModel->setTableName($request->boardName);
        $this->boardFileModel = $boardFile;
    }

    // 글 보기 중 첨부파일 다운로드
    public function download(Request $request, $boardName, $writeId, $fileNo)
    {
        $file = $this->boardFileModel->where([
            'board_id' => $this->writeModel->board->id,
            'write_id' => $writeId,
            'board_file_no' => $fileNo,
        ])->first();

        event(new \App\Events\BeforeDownload($request, $this->writeModel, $this->writeModel->board, $file));

        return response()->download(storage_path('app/public/'. $request->boardName. '/'. $file->file), $file->source);
    }

    // 글 보기 중 원본 이미지 보기
    public function viewOriginal(Request $request)
    {
        $imageName = $request->imageName;
        // dd($imageName);
        // 이미지 파일이름과 확장자를 분리
        $divImageNamesForExtension = explode('.', $imageName);
        // 확장자
        $extension = last($divImageNamesForExtension);
        // thumbnail일 경우
        $divImageNames = explode('_', $imageName);
        if(notNullCount($divImageNames) > 1) {
            if(notNullCount($divImageNames) == 2) {
                $imageName = $divImageNames[0]. '.'. $extension;
            } else {
                array_pop($divImageNames);
                $imageName = implode('', $divImageNames). '.'. $extension;
            }
        }
        $type = $request->type;

        if($type == 'editor') {
            $folder = $type;
            $imagePath = storage_path('app/public/editor/'. $imageName);
        } else {
            $folder = $request->segment(3);
            $imagePath = storage_path('app/public/'. $folder. '/'. $imageName);
        }

        $imageFileInfo = getimagesize($imagePath);
        $params = [
            'imagePath' => $folder.'/'.$imageName,
            'width' => $imageFileInfo[0],
            'height' => $imageFileInfo[1],
        ];

        return view("common.viewImage", $params);
    }

    // 이미지 업로드 페이지 열기(팝업)
    public function popup()
    {
        $theme = cache('config.theme')->name ? : 'default';

        return viewDefault("$theme.boards.uploadImage");
    }

    // 이미지 업로드 실행
    public function uploadImage(Request $request)
    {
        $imgUrl = $this->boardFileModel->storeImageFile($request);

        return $imgUrl;
    }

}
