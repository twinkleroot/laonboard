<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use File;
use Cache;
use DB;

class Content extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'contents';
    }

    // 관리자 - 내용관리 목록 가져오기
    public function getContentList()
    {
        $contents = Content::orderBy('content_id')->paginate(Cache::get('config.homepage')->pageRows);

        return $contents;
    }

    // 관리자 - 내용 추가 데이터 가져오기
    public function getContentCreate()
    {
        $skinList = getSkins('content');
        $mobileSkinList = getSkins('content');

        return [
            'skinList' => $skinList,
            'mobileSkinList' => $mobileSkinList,
            'type' => ''
        ];
    }

    // 관리자 - 내용 편집 데이터 가져오기
    public function getContentEdit($id)
    {
        $content = Content::where('content_id', $id)->first();
        $path = storage_path('app/public/content/'. $content->content_id);
        $existHeadImage = File::exists($path. '_h');
        $headImageWidth = 0;
        if($existHeadImage) {
            $headImageWidth = getimagesize($path. '_h')[0];
            if($headImageWidth > 750) {
                $headImageWidth = 750;
            }
        }

        $tailImageWidth = 0;
        $existTailImage = File::exists($path. '_t');
        if($existTailImage) {
            $tailImageWidth = getimagesize($path. '_t')[0];
            if($tailImageWidth > 750) {
                $tailImageWidth = 750;
            }
        }
        $skinList = getSkins('content');
        $mobileSkinList = getSkins('content');

        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'headImageWidth' => $headImageWidth,
            'existTailImage' => $existTailImage,
            'tailImageWidth' => $tailImageWidth,
            'skinList' => $skinList,
            'mobileSkinList' => $mobileSkinList,
            'type' => 'update'
        ];
    }

    // 내용 추가 실행
    public function storeContent($request)
    {
        $toInsert = $request->all();
        $toInsert = array_except($toInsert, ['_token', '_method', 'type', 'himg', 'timg']);

        Content::insert($toInsert);

        $this->uploadContentImage($request);

        return Content::find(DB::getPdo()->lastInsertId())->content_id;
    }

    public function updateContent($request, $id)
    {
        $toUpdate = $request->all();
        $toUpdate = array_except($toUpdate, ['_token', '_method', 'type', 'himg', 'timg', 'himg_del', 'timg_del']);

        $content = Content::find($id);
        $result = $content->update($toUpdate);

        if($request->himg_del) {
            $this->deleteContentImage($content->content_id, 'h');
        }
        if($request->timg_del) {
            $this->deleteContentImage($content->content_id, 't');
        }

        $this->uploadContentImage($request);

        if(!$result) {
            abort(500, '내용변경에 실패하였습니다.');
        }

        return $content->content_id;
    }

    // 상단, 하단이미지 업로드
    public function uploadContentImage($request)
    {
        if($request->himg) {
            $request->himg->storeAs('content', $request->content_id. '_h');
        }
        if($request->timg) {
            $request->timg->storeAs('content', $request->content_id. '_t');
        }
    }

    // 삭제 체크박스 체크시 파일 삭제
    public function deleteContentImage($contentName, $type)
    {
        $fileName = storage_path('app/public/content/'. $contentName. '_'. $type);
        if(File::exists($fileName)) {
            File::delete($fileName);
        }
    }

    // 내용 삭제
    public function deleteContent($id)
    {
        $content = Content::find($id);

        $this->deleteContentImage($content->content_id, 'h');
        $this->deleteContentImage($content->content_id, 't');
        $content->delete($id);
    }
}
