<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use File;

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

    // 내용 상세 데이터 가져오기
    public function getContentView($contentId)
    {
        $content = Content::where('content_id', $contentId)->first();
        $path = storage_path('app/public/content/'. $content->content_id);
        $existHeadImage = File::exists($path. '_h');
        $existTailImage = File::exists($path. '_t');

        // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
        $board = app()->tagged('board')[0];
        $board->image_width = config('gnu.image_width');
        $board->gallery_height = config('gnu.gallery_height');

        $content->content = convertContent($content->content, $content->html, $content->tag_filter_use);
        $content->content = includeImagePathByEditor($board, $content->content);

        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'existTailImage' => $existTailImage,
        ];
    }

    // 관리자 - 내용관리 목록 가져오기
    public function getContentList()
    {
        $contents = Content::orderBy('content_id')->paginate(cache('config.homepage')->pageRows);
        return $contents;
    }

    // 관리자 - 내용 추가 데이터 가져오기
    public function getContentCreate()
    {
        $skinList = $this->getContentSkins();
        // $mobileSkinList = $this->getContentSkins();
        return [
            'skinList' => $skinList,
            // 'mobileSkinList' => $mobileSkinList,
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
        $skinList = $this->getContentSkins();
        // $mobileSkinList = $this->getContentSkins();
        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'headImageWidth' => $headImageWidth,
            'existTailImage' => $existTailImage,
            'tailImageWidth' => $tailImageWidth,
            'skinList' => $skinList,
            // 'mobileSkinList' => $mobileSkinList,
            'type' => 'update'
        ];
    }

    // 내용 추가 실행
    public function storeContent($request)
    {
        $toInsert = $request->all();
        $toInsert = array_except($toInsert, ['_token', '_method', 'type', 'himg', 'timg']);
        $id = Content::insertGetId($toInsert);
        $this->uploadContentImage($request);
        return Content::find($id)->content_id;
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
        $content = Content::whereContentId($id)->first();
        $this->deleteContentImage($content->content_id, 'h');
        $this->deleteContentImage($content->content_id, 't');
        $content->delete();
    }

    private function getContentSkins()
    {
        $path = resource_path("views/modules/content/skins");
        // $result = [];
        $result = ['' => '선택'];
        if(File::exists($path)) {
            $dirs = File::directories($path);
            foreach($dirs as $dir) {
                $result[basename($dir)] = basename($dir);
            }
        }

        return $result;
    }
}
