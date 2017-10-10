<?php

namespace App;

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
        $board = new Board();
        $board->image_width = config('gnu.image_width');
        $board->gallery_height = config('gnu.gallery_height');

        $content->content = convertContent($content->content, $content->html);
        $content->content = includeImagePathByEditor($board, $content->content);

        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'existTailImage' => $existTailImage,
        ];
    }
}
