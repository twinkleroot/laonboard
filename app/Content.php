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
    public $primaryKey = 'id';
    public $timestamps = false;

    public function contentList()
    {
        $counts = Content::all();
        $ids = ['company', 'privacy', 'provision'];
        $subjects = ['회사소개', '개인정보 처리방침', '서비스 이용약관'];
        $contents = [
            '<p align=center><b>회사소개에 대한 내용을 입력하십시오.</b></p>',
            '<p align=center><b>개인정보 처리방침에 대한 내용을 입력하십시오.</b></p>',
            '<p align=center><b>서비스 이용약관에 대한 내용을 입력하십시오.</b></p>'
        ];
        $contentModel = [];
        for($i=0; $i<3; $i++) {
            $contentModel[] = Content::firstOrCreate([
                'content_id' => $ids[$i],
                'html' => 1,
                'subject' => $subjects[$i],
                'content' => $contents[$i]
            ]);
        }

        return $contentModel;
    }

    public function viewContent($id)
    {
        $content = Content::where('content_id', $id)->first();
        $path = storage_path('app/public/content/'. $content->content_id);
        $headPath = $path. '_h';
        $tailPath = $path. '_t';
        $existHeadImage = File::exists($headPath);
        $existTailImage = File::exists($tailPath);

        if( is_null($content->skin) ) {
            $content->skin = 'default';
        }

        return [
            'content' => $content,
            'existHeadImage' => $existHeadImage,
            'existTailImage' => $existTailImage,
            'skinName' => $content->skin
        ];
    }

}
