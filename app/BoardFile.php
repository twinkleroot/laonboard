<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Storage;
use App\Board;

class BoardFile extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $primaryKey = 'board_file_no, board_id, write_id';

    public $timestamps = false;

    public function storeBoardFile($request, $boardId, $lastInsertId)
    {
        $board = Board::find($boardId);
        $maxUploadSize = $board->upload_size;
        $files = $request->attach_file;
        $message = '';
        $index = 0;
        foreach($files as $file) {
            $image = getimagesize($file);
            $fileSize = filesize($file);
            $originalFileName = $file->getClientOriginalName();
            $hashName = $file->hashName();
            $width = 0;
            $height = 0;
            $type = 0;

            if( $fileSize > $maxUploadSize && !session()->get('admin') ) {
                $message .= $originalFileName . ' 파일의 용량(' . number_format($fileSize) . ' 바이트)이 서버에 설정(' . number_format($maxUploadSize) . ' 바이트)된 값보다 크므로 업로드 할 수 없습니다.\\n';
                continue;
            }

            if($image) {
                $width = $image[0];
                $height = $image[1];
                $type = $image[2];
            }

            // 로컬 저장소에 파일 저장, config/filesystems.php에서 설정 가능
            // 기본 설정 : storage/app/게시판테이블명/파일이름 으로 저장됨
            $file->storeAs(Board::find($boardId)->table_name, $file->hashName());

            $uploadFile = [
                'board_id' => $boardId,
                'write_id' => $lastInsertId,
                'board_file_no' => $index,
                'source' => $originalFileName,
                'file' => $hashName,
                'content' => $request->has('file_content') ? $request->file_content[$index] : null,
                'filesize' => $fileSize,
                'width' => $width,
                'height' => $height,
                'type' => $type,
                'created_at' => Carbon::now(),
            ];
            $index++;

            $boardFile = BoardFile::create($uploadFile);
        }

        return $message;
    }

}
