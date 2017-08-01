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
        $divImageNamesForExtension = explode('.', $imageName);
        $extension = $divImageNamesForExtension[count($divImageNamesForExtension) - 1];
        $divImageNames = explode('_', $imageName);
        if(count($divImageNames) <= 2) {
            $imageName = $divImageNames[0]. '.'. $extension;
        } else {
            array_pop($divImageNames);
            $imageName = implode('', $divImageNames). '.'. $extension;
        }
        $type = $request->type;

        if($type == 'editor') {
            $folder = $type;
            $imagePath = storage_path('app/public/editor/'. $imageName);
        } else {
            $folder = $request->boardId;
            $imagePath = storage_path('app/public/'. $folder. '/'. $imageName);
        }

        $imageFileInfo = getimagesize($imagePath);
        $params = [
            'imagePath' => $folder.'/'.$imageName,
            'width' => $imageFileInfo[0],
            'height' => $imageFileInfo[1],
        ];

        return view('board.viewImage', $params);
    }

    // 이미지 업로드 페이지 열기(팝업)
    public function popup()
    {
        return view('board.uploadImage');
    }

    // 이미지 업로드 실행
    public function uploadImage(Request $request)
    {
        $imgUrl = $this->boardFileModel->storeImageFile($request);

        return $imgUrl;
    }

}
