<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use File;
use Storage;

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

    public function __construct()
    {
        $this->table = 'board_files';
    }

    // 글쓰기 할때 파일 업로드 갯수 검사 후 업로드 기능 호출
    public function createBoardFiles($request, $boardId, $writeId)
    {
        $board = Board::getBoard($boardId);
        $files = $request->attach_file;
        $fileCount = notNullCount($files);
        if($fileCount > $board->upload_count) {
            abort(500, '첨부파일을 '.number_format($board->upload_count).'개 이하로 업로드 해주십시오.');
        } else {
            $uploadFileInfos = array();
            for($i=0; $i<$fileCount; $i++) {
                array_push($uploadFileInfos, $this->getToUploadFileInfo($request, $writeId, $i));
            }

            // 서버에 파일 업로드
            $this->uploadFiles($request, $board->table_name);
            // 파일 테이블에 기록
            $this->insertBoardFiles($uploadFileInfos);
        }
    }

    // 글수정 할때 파일 업로드 갯수 검사 후 업로드 기능 호출
    public function updateBoardFiles($request, $boardId, $writeId)
    {
        $board = Board::getBoard($boardId);
        $files = $request->attach_file;
        $fileCount = notNullCount($files);
        if($fileCount && $fileCount > $board->upload_count) {
            abort(500, '기존 파일을 삭제하신 후 첨부파일을 '.number_format($board->upload_count).'개 이하로 업로드 해주십시오.');
        } else {
            for($i=0; $i<notNullCount($_FILES['attach_file']['name']); $i++) {
                // 파일 삭제 체크가 되어 있으면
                if (isset($request->file_del[$i]) && $request->file_del[$i] > 0) {
                    // 삭제할 파일정보를 선택한다.
                    $delFile = $this->selectBoardFile($boardId, $writeId, $i)->first();
                    // 기존 파일과 썸네일을 삭제한다.
                    $this->deleteFileOnServer($board, $board->table_name, $delFile->file);
                    // 파일 테이블의 해당 행을(board_file_no) 삭제한다.
                    if( $this->selectBoardFile($boardId, $writeId, $i)->delete() < 1) {
                        abort(500, '파일정보 삭제에 실패하였습니다.');
                    }
                }
                // 새 파일 업로드를 했을 때
                if(isset($files[$i])) {
                    // 추가하거나 수정될 파일의 정보
                    $newFile = $this->getToUploadFileInfo($request, $writeId, $i);
                    // 교체할 파일정보를 선택한다.
                    $delFile = $this->selectBoardFile($boardId, $writeId, $i)->first();
                    // 기존에 파일이 존재하는 번호(board_file_no)라면
                    if( !is_null($delFile) ) {
                        // 기존 파일과 썸네일을 삭제한다.
                        $this->deleteFileOnServer($board, $board->table_name, $delFile->file);
                        // 선택한 파일을 업로드 한다.
                        if( !$this->uploadFile($files[$i], $board->table_name) ) {
                            abort(500, '선택한 파일을 업로드하는데 실패했습니다.');
                        }
                        // 파일 테이블의 해당 행을(board_file_no) 수정한다.
                        if ($this->selectBoardFile($boardId, $writeId, $i)->update($newFile) < 1) {
                            abort(500, '선택한 파일의 정보를 수정하는데 실패했습니다.');
                        }
                    } else { // 비어있는 번호라면
                        // 선택한 파일을 업로드 한다.
                        if( !$this->uploadFile($files[$i], $board->table_name) ) {
                            abort(500, '선택한 파일을 업로드하는데 실패했습니다.');
                        }
                        // 파일 테이블의 해당 행을(board_file_no) 삽입한다.
                        if( !$this->selectBoardFile($boardId, $writeId, $i)->insert($newFile) ) {
                            abort(500, '선택한 파일의 정보를 추가하는데 실패했습니다.');
                        }
                    }
                }
            }

            // 삭제 후 비어 있는 board_file_no를 업데이트 한다.
            $updateAllFiles = BoardFile::where(['board_id' => $boardId, 'write_id' => $writeId])->get();
            $index = 0;
            foreach($updateAllFiles as $updateAllFile) {
                $this
                ->selectBoardFile($boardId, $writeId, $updateAllFile->board_file_no)
                ->update(['board_file_no' => $index]);

                $index++;
            }

        }
        return $updateAllFiles->count();
    }

    // BoardFile의 Primary key where절 까지의 Builder
    private function selectBoardFile($boardId, $writeId, $fileNo)
    {
        return BoardFile::where([
            'board_id' => $boardId,
            'write_id' => $writeId,
            'board_file_no' => $fileNo
        ]);
    }

    // 업로드할 파일의 정보를 배열에 저장한다.
    private function getToUploadFileInfo($request, $writeId, $index)
    {
        $file = $request->attach_file[$index];
        $uploadFileInfo = array();
            // 업로드할 파일과 서버의 설정을 비교 검사하고 업로드 가능한 이미지 가져옴
            $image = $this->checkServerUploadError($file);

            $uploadFileInfo = [
                'board_id' => Board::getBoard($request->boardName, 'table_name')->id,
                'write_id' => $writeId,
                'board_file_no' => $index,
                'source' => $file->getClientOriginalName(),
                'file' => $file->hashName(),
                'content' => $request->filled('file_content') ? $request->file_content[$index] : null,
                'filesize' => filesize($file),
                'width' => $image ? $image[0] : 0,
                'height' => $image ? $image[1] : 0,
                'type' => $image ? $image[2] : 0,
                'created_at' => Carbon::now(),
            ];

        return $uploadFileInfo;
    }

    // 업로드할 파일과 서버의 설정을 비교 검사
    private function checkServerUploadError($file)
    {
        $fileName = $file->getClientOriginalName();
        $error = $file->getError();
        if($error == 1) {
            abort(500, '\"'.$fileName.'\" 파일의 용량이 서버에 설정('.ini_get('upload_max_filesize').')된 값보다 크므로 업로드 할 수 없습니다.\\n');
        } else if($error != 0) {
            abort(500, '\"'.$fileName.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n');
        }

        $image = @getimagesize($file);
        $imageExtension = cache('config.board')->imageExtension;
        $flashExtension = cache('config.board')->flashExtension;
        if ( preg_match("/\.({$imageExtension})$/i", $fileName) || preg_match("/\.({$flashExtension})$/i", $fileName) ) {
            if ($image[2] < 1 || $image[2] > 16) {
                // 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
                // 에러메세지는 출력하지 않는다.
                throw new Exception('');
            }
        }

        return $image;
    }

    // 서버에 파일 업로드 (단수)
    public function uploadFile($file, $folderName)
    {
        return $this->storePublicly($file, $folderName, $file->hashName());
    }

    // 서버에 파일 업로드 (복수)
    public function uploadFiles($request, $folderName)
    {
        $uploadFiles = $request->attach_file;
        $result;
        foreach($uploadFiles as $uploadFile) {
            $result = $this->storePublicly($uploadFile, $folderName, $uploadFile->hashName());
        }

        return $result;
    }

    // 파일권한을 public(644)으로 업로드한다.
    public function storePublicly($file, $folderName, $fileName, $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';	// private : 600, public : 644

        return $file->storeAs($folderName, $fileName, $options);
    }

    /**
     * Parse and format the given options. by SymfonyUploadedFile
     *
     * @param  array|string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }

        return $options;
    }

    // 파일 업로드 및 파일테이블에 저장 (복수)
    public function insertBoardFiles($uploadFileInfos)
    {
        foreach($uploadFileInfos as $uploadFileInfo) {
            $uploadFileInfo['created_at'] = Carbon::now();
            BoardFile::insert($uploadFileInfo);
        }
    }

    // 기존 파일과 썸네일을 삭제한다.
    public function deleteFileOnServer($board, $tableName, $delFileName)
    {
        // 기존 파일을 삭제한다.
        $dir = storage_path('app/public/'. $tableName);
        $path = "$dir/$delFileName";
        if(!File::exists($path)) {
            return 1;
        }
        if(getimagesize($path)) {
            // 기존 썸네일을 삭제한다.
            $thumbSize = getViewThumbnail($board, $delFileName, $tableName);
            $thumbName = $thumbSize['name'];
            $thumbnailPath =  "$dir/$thumbName";
            $returnVal = File::delete($thumbnailPath);
        }
        $returnVal = File::delete($path);

        return $returnVal;
    }

    // 게시물 삭제 할 때 첨부 파일 삭제
    public function deleteWriteAndAttachFile($boardId, $writeId, $type='')
    {
        $board = Board::getBoard($boardId);
        $delFiles = BoardFile::where([
            'board_id' => $boardId,
            'write_id' => $writeId,
        ])->get();

        if(notNullCount($delFiles) < 1) {
            return true;
        }

        // 첨부 파일 삭제
        $result = $this->deleteAttachFile($board, $board->table_name, $delFiles);

        if(!$result) {
            return $result;
        }

        // 첨부 파일 정보 삭제
        $result = BoardFile::where([
            'board_id' => $boardId,
            'write_id' => $writeId,
        ])->delete() > 0 ? true : false;

        return $result;
    }

    // 첨부 파일 삭제
    private function deleteAttachFile($board, $tableName, $delFiles)
    {
        $result = true;
        // 첨부 파일 삭제
        if(notNullCount($delFiles) > 0) {
            foreach ($delFiles as $delFile) {
                $result = $this->deleteFileOnServer($board, $tableName, $delFile->file) > 0 ? true : false;
            }
        }

        return $result;
    }

    // 에디터로 첨부한 이미지 파일 서버에 저장
    public function storeImageFile($request)
    {
        $files = $request->imageFile;
        $imgUrl = [];

        foreach($files as $file) {
            if($file) {
                $this->storePublicly($file, 'editor', $file->hashName());

                array_push($imgUrl, '/storage/editor/'.$file->hashName());
            }
        }

        return $imgUrl;
    }

}
