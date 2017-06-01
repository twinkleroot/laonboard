<?php

namespace App\Common;

use Illuminate\Http\Request;
use Cache;

// 제목과 내용에 금지단어가 있는지 검사
class Filter
{
    public function filter(Request $request)
    {
        $subject = $request->subject;
        $content = $request->content;

        $filterStrs = explode(',', trim(implode(',', Cache::get("config.board")->filter)));
        $returnArr['subject'] = '';
        $returnArr['content'] = '';
        foreach($filterStrs as $str) {
            // 제목 필터링 (찾으면 중지)
            $pos = stripos($subject, $str);
            if ($pos !== false) {
                $returnArr['subject'] = $str;
                break;
            }

            // 내용 필터링 (찾으면 중지)
            $pos = stripos($content, $str);
            if ($pos !== false) {
                $returnArr['content'] = $str;
                break;
            }
        }

        return $returnArr;
    }
}
