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
        $files = $request->attach_file;
        foreach($files as $file) {
            $image = getimagesize($file);
            $fileSize = filesize($file);
            $originalFileName = $file->getClientOriginalName();
            $width = 0;
            $height = 0;
            $type = 0;
            if($image) {
                $width = $image[0];
                $height = $image[1];
                $type = $image[2];
            }
            dd($originalFileName, $fileSize, $image);
            $file->storeAs(Board::find($boardId)->table_name, $file->hashName());
            dump($file);
            dd($file->hashName());
            // dd($file->path(), $file->get('originalName'), $file->get('type'), $file->get('size'));

            // $uploadFile = [
            //     'id' => $loop->index,
            //     'board_id' => $boardId,
            //     'write_id' => $lastInsertId,
            //     'source' => $originalFileName,
            //     'file' => $file->get('originalName'),
            //     'download' => 0,
            //     'content' =>   ,            // 파일 설명을 쓰는 경우
            //     'filesize' => $fileSize,
            //     'width' => $width,
            //     'height' => $height,
            //     'type' => $type,
            //     'created_at' => Carbon::now(),
            //
            // ];
        }
        // dump($file[0]);
        // dump($file[0]->extension());
        // dump($request->hasFile('attach_file'));
        // dd($file);
    }
}
