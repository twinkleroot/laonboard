<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Storage;
use File;
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

    // 글쓰기 할때 파일 업로드 갯수 검사 후 업로드 기능 호출
    public function createBoardFiles($request, $boardId, $writeId)
    {
        $board = Board::find($boardId);
        $files = $request->attach_file;
        $fileCount = count($files);
        if($fileCount > $board->upload_count) {
            return '첨부파일을 '.number_format($board->upload_count).'개 이하로 업로드 해주십시오.';
        } else {
            $uploadFileInfos = array();
            for($i=0; $i<$fileCount; $i++) {
                array_push($uploadFileInfos, $this->getToUploadFileInfo($request, $writeId, $i));
            }

            $message = $this->makeReturnMessage($uploadFileInfos); // 리턴시킬 메세지 배열에서 뽑아내서 생성
            if($message != '') {
                return $message;
            }

            // 서버에 파일 업로드
            $this->uploadFiles($request, $board->table_name);
            // 파일 테이블에 기록
            $this->insertBoardFiles($uploadFileInfos);

            return $message;
        }
    }

    // 글수정 할때 파일 업로드 갯수 검사 후 업로드 기능 호출
    public function updateBoardFiles($request, $boardId, $writeId)
    {
        $board = Board::find($boardId);
        $files = $request->attach_file;
        $fileCount = count($files);
        if($fileCount && $fileCount > $board->upload_count) {
            return ['message' => '기존 파일을 삭제하신 후 첨부파일을 '.number_format($board->upload_count).'개 이하로 업로드 해주십시오.'];
        } else {
            for($i=0; $i<count($_FILES['attach_file']['name']); $i++) {
                // 파일 삭제 체크가 되어 있으면
                if (isset($request->file_del[$i]) && $request->file_del[$i] > 0) {
                    // 삭제할 파일정보를 선택한다.
                    $delFile = $this->selectBoardFile($boardId, $writeId, $i)->first();
                    // 기존 파일과 썸네일을 삭제한다.
                    if( !$this->deleteFileOnServer($board->table_name, $delFile->file) ) {
                        return ['message' => '업로드한 파일을 삭제하는데 실패하였습니다.'];
                    }
                    // 파일 테이블의 해당 행을(board_file_no) 삭제한다.
                    if( $this->selectBoardFile($boardId, $writeId, $i)->delete() < 1) {
                        return ['message' => '파일정보 삭제에 실패하였습니다.'];
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
                        if( !$this->deleteFileOnServer($board->table_name, $delFile->file) ) {
                            return ['message' => '기존 파일을 삭제하는데 실패하였습니다.'];
                        }
                        // 선택한 파일을 업로드 한다.
                        if( !$this->uploadFile($files[$i], $board->table_name) ) {
                            return ['message' => '선택한 파일을 업로드하는데 실패했습니다.'];
                        }
                        // 파일 테이블의 해당 행을(board_file_no) 수정한다.
                        if ($this->selectBoardFile($boardId, $writeId, $i)->update($newFile) < 1) {
                            return ['message' => '선택한 파일의 정보를 수정하는데 실패했습니다.'];
                        }
                    } else { // 비어있는 번호라면
                        // 선택한 파일을 업로드 한다.
                        if( !$this->uploadFile($files[$i], $board->table_name) ) {
                            return ['message' => '선택한 파일을 업로드하는데 실패했습니다.'];
                        }
                        // 파일 테이블의 해당 행을(board_file_no) 삽입한다.
                        if( !$this->selectBoardFile($boardId, $writeId, $i)->insert($newFile) ) {
                            return ['message' => '선택한 파일의 정보를 추가하는데 실패했습니다.'];
                        }
                    }
                }
            }

            // 삭제 후 비어 있는 board_file_no를 업데이트 한다.
            $updateAllFiles = BoardFile::where(['board_id' => $boardId, 'write_id' => $writeId])->get();
            $index = 0;
            foreach($updateAllFiles as $updateAllFile) {
                $this->selectBoardFile($boardId, $writeId, $updateAllFile->board_file_no)
                ->update(['board_file_no' => $index]);
                $index++;
            }

        }
        return ['fileCount' => $updateAllFiles->count()];
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
            // 서버에 설정된 값보다 큰파일을 업로드 한다면
            $message = $this->checkServerUploadError($file);
            if($message) {
                $uploadFileInfo['message'] = $message;
                return $uploadFileInfo;
            }
            $image = getimagesize($file);
            $uploadFileInfo = [
                'board_id' => $request->boardId,
                'write_id' => $writeId,
                'board_file_no' => $index,
                'source' => $file->getClientOriginalName(),
                'file' => $file->hashName(),
                'content' => $request->has('file_content') ? $request->file_content[$index] : null,
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
            return '\"'.$fileName.'\" 파일의 용량이 서버에 설정('.ini_get('upload_max_filesize').')된 값보다 크므로 업로드 할 수 없습니다.\\n';
        } else if($error != 0) {
            return '\"'.$fileName.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
        }
    }

    // 서버에 파일 업로드 (단수)
    public function uploadFile($file, $folderName)
    {
        return $file->storeAs($folderName, $file->hashName());
    }

    // 서버에 파일 업로드 (복수)
    public function uploadFiles($request, $folderName)
    {
        $uploadFiles = $request->attach_file;
        $result;
        foreach($uploadFiles as $uploadFile) {
            $result = $uploadFile->storeAs($folderName, $uploadFile->hashName());
        }

        return $result;
    }

    // 파일 업로드 및 파일테이블에 저장 (복수)
    public function insertBoardFiles($uploadFileInfos)
    {
        foreach($uploadFileInfos as $uploadFileInfo) {
            $boardFile = BoardFile::create($uploadFileInfo);
        }
    }

    // 리턴시킬 메세지 생성
    private function makeReturnMessage($uploadFileInfos)
    {
        $message = '';
        foreach($uploadFileInfos as $uploadFileInfo) {
            if(isset($uploadFileInfo['message']) && $uploadFileInfo['message'] != '') {
                $message .= $uploadFileInfo['message'];
            }
        }
        return $message;
    }

    // 기존 파일과 썸네일을 삭제한다.
    public function deleteFileOnServer($tableName, $delFileName)
    {
        // 기존 파일을 삭제한다.
        $path = storage_path('app/public/'. $tableName);
        $pathAndFile = $path. '/'. $delFileName;
        // 기존 썸네일을 삭제한다.
        $pathAndThumbnail =  $path. '/thumb-'. $delFileName;

        $returnVal = File::delete($pathAndFile);
        if(File::exists($pathAndThumbnail)) {
            $returnVal = File::delete($pathAndThumbnail);
        }

        return $returnVal;
    }

    // 게시물 삭제 할 때 첨부 파일, 에디터 첨부파일도 함께 삭제
    public function deleteWriteAndAttachFile($boardId, $writeId, $type='')
    {
        $board = Board::find($boardId);
        $writeModel = new Write($boardId);
        $writeModel->setTableName($board->table_name);
        $write = $writeModel->find($writeId);
        $content = $write->content;
        $delFiles = BoardFile::where([
                        'board_id' => $boardId,
                        'write_id' => $writeId,
                ])->get();

        $result = array();
        // 첨부 파일 삭제
        $index = 0;
        $result[$index++] = $this->deleteAttachFile($board->table_name, $delFiles);
        // 에디터에 첨부된 이미지와 썸네일 삭제
        if($type != 'move') {
            $result[$index++] = $this->deleteEditorImage($content);
        }
        // 파일 테이블 삭제
        $result[$index] = BoardFile::where([
                        'board_id' => $boardId,
                        'write_id' => $writeId,
                    ])
                    ->delete() > 0 ? true : false;

        return $result;
    }

    // 첨부 파일 삭제
    private function deleteAttachFile($tableName, $delFiles)
    {
        $result = true;
        // 첨부 파일 삭제
        if(count($delFiles) > 0) {
            foreach ($delFiles as $delFile) {
                $result = $this->deleteFileOnServer($tableName, $delFile->file);
            }
        }

        return $result;
    }

    // 에디터에 첨부된 이미지와 썸네일 삭제
    private function deleteEditorImage($content)
    {
        // 에디터로 업로드한 이미지 경로를 추출한다.
        $pattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
        preg_match_all($pattern, $content, $matches);
        // 에디터에 첨부된 이미지와 썸네일 삭제
        foreach ($matches[1] as $match) {
            $basename = basename($match);
            if(strpos($basename, "thumb") !== false) {
                $basename = substr($basename, 6);
            }
            $result = $this->deleteFileOnServer('editor', $basename);
        }
    }

    // 에디터로 첨부한 이미지 파일 서버에 저장
    public function storeImageFile($request)
    {
        $files = $request->imageFile;
        $imgUrl = [];

        foreach($files as $file) {
            $file->storeAs('editor', $file->hashName());

            array_push($imgUrl, '/storage/editor/'.$file->hashName());
        }

        return $imgUrl;
    }

}
