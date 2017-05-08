<?php

namespace App\Common;

// 공통으로 사용하는 메서드
class Util
{
    // 입력 안된 필드( == null )는 입력값에서 제외.
    public static function exceptNullData($data)
    {
        foreach($data as $key => $value) {
            if(is_null($value)) {
                $data = array_except($data, $key);
            }
        }
        return $data;
    }

    // 그누보드의 common.lib.php에 있는 conv_content 함수
    public static function convertContent($content, $html)
    {
        if($html){
            $source = array();
            $target = array();

            $source[] = "//";
            $target[] = "";

            if ($html == 2) { // 자동 줄바꿈
                $source[] = "/\n/";
                $target[] = "<br>";
            }

            // 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
            // $table_begin_count = substr_count(strtolower($content), "<table");
            // $table_end_count = substr_count(strtolower($content), "</table");
            // for ($i=$table_end_count; $i<$table_begin_count; $i++)
            // {
            //     $content .= "</table>";
            // }

            $content = preg_replace($source, $target, $content);

            // if($filter)
            //     $content = html_purifier($content);
        }    // else // text 이면
        // {
        //     // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
        //     $content = html_symbol($content);
        //
        //     // 공백 처리
    	// 	//$content = preg_replace("/  /", "&nbsp; ", $content);
    	// 	$content = str_replace("  ", "&nbsp; ", $content);
    	// 	$content = str_replace("\n ", "\n&nbsp;", $content);
        //
        //     $content = get_text($content, 1);
        //     $content = url_auto_link($content);
        // }

        return $content;
    }
}
