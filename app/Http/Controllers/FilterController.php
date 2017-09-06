<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FilterController extends Controller
{
    // 게시글에 제목과 내용에 금지단어가 포함되어있는지 검사
    public function boardFilter(Request $request)
    {
        $subject = $request->subject;
        $content = $request->content;

        $filterStrs = explode(',', trim(implode(',', cache("config.board")->filter)));
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

    // 닉네임이 금지단어와 같은지 검사
    public function userFilter(Request $request)
    {
        $nick = $request->nick;

        $filterStrs = explode(',', trim(implode(',', cache("config.join")->banId)));
        $returnArr['nick'] = '';

        foreach($filterStrs as $str) {
            // 제목 필터링 (찾으면 중지)
            $pos = stripos($nick, $str);
            if ($pos !== false) {
                $returnArr['nick'] = $str;
                break;
            }

        }

        return $returnArr;
    }
}
