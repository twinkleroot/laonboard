<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Storage;

class BoardFile extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function storeBoardFile($request, $boardId, $lastInsertId)
    {
        $board = Board::find($boardId);
        $maxUploadSize = $board->upload_size;
        $files = $request->attach_file;
        $message = '';
        foreach($files as $file) {
            $image = getimagesize($file);
            $fileSize = filesize($file);
            $originalFileName = $file->getClientOriginalName();
            $hashName = $file->hashName();
            $width = 0;
            $height = 0;
            $type = 0;

            if($fileSize > $maxUploadSize) {
                $message .= '\"'.$originalFileName.'\" 파일의 용량이 서버에 설정('.$maxUploadSize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
                continue;
            }

            if($image) {
                $width = $image[0];
                $height = $image[1];
                $type = $image[2];
            }

            // 저장소에 파일 저장, config/filesystems.php에서 설정 가능
            // 기본 설정 : storage/app/게시판테이블명/파일이름 으로 저장됨
            $file->storeAs(Board::find($boardId)->table_name, $file->hashName());

            $uploadFile = [
                'id' => $loop->index,
                'board_id' => $boardId,
                'write_id' => $lastInsertId,
                'source' => $originalFileName,
                'file' => $hashName,
                'download' => 0,
                'content' => $request->has('file_content') ? $request->content[$loop->index] : '',            // 파일 설명을 쓰는 경우
                'filesize' => $fileSize,
                'width' => $width,
                'height' => $height,
                'type' => $type,
                'created_at' => Carbon::now(),
            ];
        }

        return $message;
    }

}
