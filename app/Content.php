<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use App\Common\Util;

class Content extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $primaryKey = 'id';
    public $timestamps = false;

    // 관리자 - 내용관리 목록 가져오기
    public function getContentList()
    {
        $contents = Content::all();

        return $contents;
    }

    // 내용 상세 데이터 가져오기
    public function getContentView($id)
    {
        $content = Content::where('content_id', $id)->first();
        $path = storage_path('app/public/content/'. $content->content_id);
        $existHeadImage = File::exists($path. '_h');
        $existTailImage = File::exists($path. '_t');

        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'existTailImage' => $existTailImage,
        ];
    }

    public function getContentCreate()
    {
        $skinList = Util::getSkins();
        // 모바일 스킨이 저장되는 경로를 정해야 함 (임시로 PC스킨과 동일하게)
        $mobileSkinList = Util::getSkins();

        return [
            'skinList' => $skinList,
            'mobileSkinList' => $mobileSkinList,
            'type' => ''
        ];
    }

    // 내용 편집 데이터 가져오기
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
        $skinList = Util::getSkins();
        // 모바일 스킨이 저장되는 경로를 정해야 함 (임시로 PC스킨과 동일하게)
        $mobileSkinList = Util::getSkins();

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
        $content = Content::where('content_id', $request->content_id)->first();
        if($content) {
            return '이미 같은 ID로 등록된 내용이 있습니다.';
        }
        $toInsert = $request->all();
        $toInsert = array_except($toInsert, ['_token', '_method', 'type']);
        return Content::create($toInsert)->content_id;
    }

}
