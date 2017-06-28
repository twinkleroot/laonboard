<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BoardFile;

class ImageController extends Controller
{
    public $boardFileModel;

    public function __construct(BoardFile $boardFile)
    {
        $this->boardFileModel = $boardFile;
    }

    // 글 보기 중 원본 이미지 보기
    public function viewOriginal(Request $request)
    {
        $imageName = $request->imageName;
        $type = $request->type;

        if($type == 'editor') {
            $folder = $type;
            $imagePath = storage_path('app/public/editor/'. $imageName);
        } else {
            $folder = $request->boardId;
            $imagePath = storage_path('app/public/'. $folder. '/'. $imageName);
        }

        $imageFileInfo = getimagesize($imagePath);

        return view('board.viewImage', [
            'imagePath' => $folder.'/'.$imageName,
            'width' => $imageFileInfo[0],
            'height' => $imageFileInfo[1],
        ]);
    }

    // 이미지 업로드 페이지 열기(팝업)
    public function popup()
    {
        return view('board.default.uploadImage');
    }

    // 이미지 업로드 실행
    public function uploadImage(Request $request)
    {
        $imgUrl = $this->boardFileModel->storeImageFile($request);

        return $imgUrl;
    }

}
