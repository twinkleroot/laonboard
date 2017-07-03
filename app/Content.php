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
    protected $table = 'contents';

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
}
