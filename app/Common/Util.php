<?php

namespace App\Common;

use Carbon\Carbon;
use Cache;
use Image;
use File;
use App\Write;
use App\Board;
use App\ManageAuth;

class Util
{
    // 관리 권한 설정 데이터를 가져온다.
    public static function getManageAuthModel($menuCode)
    {
        $manageAuth = ManageAuth::
            where([
                'user_id' => auth()->user()->id,
                'menu' => $menuCode[0],
            ])
            ->where('auth', 'like', '%'. $menuCode[1]. '%')
            ->first();

        return $manageAuth;
    }

    // 게시판 캐시 삭제
    public static function deleteCache($base, $boardTableName)
    {
        $cacheName = $base. '-'. $boardTableName;
        Cache::forget($cacheName);
    }

    // 스킨 목록을 가져온다.
    public static function getSkins($type)
    {
        $path = resource_path('views/'.$type);
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

    // 글쓰기 간격 검사
    public static function checkWriteInterval()
    {
        $dt = Carbon::now();
        $interval = Cache::get("config.board")->delaySecond;

        if(session()->has('postTime')) {
            if(session()->get('postTime') >= $dt->subSecond($interval) && !session()->get('admin')) {
                return false;
            }
        }
        session()->put('postTime', Carbon::now());

        return true;
    }

    // 올바르지 않은 코드가 글 내용에 다수 들어가 있는지 검사
    public static function checkIncorrectContent($request)
    {
        if (substr_count($request->content, '&#') > 50) {
            return false;
        }
        return true;
    }

    // 서버에서 지정한 Post의 최대 크기 검사
    public static function checkPostMaxSize($request)
    {
        if (empty($_POST)) {
            return false;
        }
        return true;
    }

    // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
    public static function checkAdminAboutNotice($request)
    {
        if ( !session()->get('admin') && $request->has('notice') ) {
    		return false;
        }
        return true;
    }

    // 파일 사이즈 구하기
    public static function getFileSize($size)
    {
        if ($size >= 1048576) {
            $size = number_format($size/1048576, 1) . "M";
        } else if ($size >= 1024) {
            $size = number_format($size/1024, 1) . "K";
        } else {
            $size = number_format($size, 0) . "byte";
        }
        return $size;
    }

    // UTF-8 문자열 자르기
    // 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
    public static function utf8Strcut($str, $size, $suffix='...' )
    {
            $substr = substr( $str, 0, $size * 2 );
            $multiSize = preg_match_all( '/[\x80-\xff]/', $substr, $multiChars );

            if ( $multiSize > 0 )
                $size = $size + intval( $multiSize / 3 ) - 1;

            if ( strlen( $str ) > $size ) {
                $str = substr( $str, 0, $size );
                $str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
                $str .= $suffix;
            }

            return $str;
    }

    // 쿼리 스트링에서 파라미터 구하기
    public static function getParamsFromQueryString($str)
    {
        $queryStrPiece = explode('&', urldecode($str));
        $params = [];
        if(count($queryStrPiece) > 1) {
            foreach($queryStrPiece as $queryStr) {
                $tmp = explode('=', $queryStr);
                $params = array_add($params, $tmp[0], $tmp[1]);
            }
        }

        return $params;
    }

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

    public static function searchKeyword($keyword, $subject)
    {
        // 문자앞에 \ 를 붙입니다.
        $src = array('/', '|');
        $dst = array('\/', '\|');

        if (!trim($keyword)) return $subject;

        // 검색어 전체를 공란으로 나눈다
        $s = explode(' ', $keyword);

        // "/(검색1|검색2)/i" 와 같은 패턴을 만듬
        $pattern = '';
        $bar = '';
        for ($m=0; $m<count($s); $m++) {
            if (trim($s[$m]) == '') continue;
            $tmp_str = quotemeta($s[$m]);
            $tmp_str = str_replace($src, $dst, $tmp_str);
            $pattern .= $bar . $tmp_str . "(?![^<]*>)";
            $bar = "|";
        }

        // 지정된 검색 폰트의 색상, 배경색상으로 대체
        $replace = "<span class=\"sch_key\">\\1</span>";

        return preg_replace("/($pattern)/i", $replace, $subject);
    }

    // 글 내용 변환
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

            $content = preg_replace($source, $target, $content);

        } else { // text 이면
            // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
            $content = static::htmlSymbol($content);

            // 공백 처리
    		$content = str_replace("  ", "&nbsp; ", $content);
    		$content = str_replace("\n ", "\n&nbsp;", $content);

            $content = static::getText($content, 1);
            $content = static::urlAutoLink($content);
        }

        return $content;
    }

    public static function htmlSymbol($str)
    {
        return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
    }

    // Text 형식으로 변환
    public static function getText($str, $html=0, $restore=false)
    {
        $source[] = "<";
        $target[] = "&lt;";
        $source[] = ">";
        $target[] = "&gt;";
        $source[] = "\"";
        $target[] = "&#034;";
        $source[] = "\'";
        $target[] = "&#039;";

        if($restore) {
            $str = str_replace($target, $source, $str);
        }

        // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
        if ($html == 0) {
            $str = static::htmlSymbol($str);
        }

        if ($html) {
            $source[] = "\n";
            $target[] = "<br>";
        }

        return str_replace($source, $target, $str);
    }

    // 문자열 자리수로 자르기(charset = 'utf-8')
    public static function cutString($str, $len, $suffix="…")
    {
        $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        $strLength = mb_strlen($str, 'UTF-8');

        if ($strLength >= $len) {
            $str = mb_substr($str, 0, $len, 'UTF-8');

            return $str . ($strLength > $len ? $suffix : '');
        } else {
            return $str;
        }
    }

    public static function urlAutoLink($str)
    {
        $config = Cache::get("config.board");

        $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
        $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"{$config->linkTarget}\">\\2</A>", $str);
        $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"{$config->linkTarget}\">\\2</A>", $str);
        $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
        $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

        return $str;
    }

    public static function getViewThumbnail($board, $imageName, $folder)
    {
        $imgPath = storage_path('app/public/'. $folder);

        $imgPathAndFileName = $imgPath. '/'. $imageName;
        $img = Image::make(file_get_contents($imgPathAndFileName));
        $thumbWidth = $board->image_width;

        // 이미지 정보를 얻어온다.
        $size = getimagesize($imgPathAndFileName);
        $size = array_add($size, 'name', $imageName);

        if(empty($size)) {
            return [];
        }
        // GIF 체크
        if($size[2] == 1) {
            return $size;
        }

        // 원본 width가 thumb_width보다 작다면 썸네일을 만들지 않는다.
        if($size[0] <= $thumbWidth) {
            return $size;
        }

        $thumbFilePath = $imgPath. '/thumb-'. $imageName;
        if( !file_exists($thumbFilePath) ) {
            if($size[2] == 2 && function_exists('exif_read_data')) {
                $degree = 0;
                $exif = @exif_read_data($imgPathAndFileName);

                if(!empty($exif['Orientation'])) {
                    switch($exif['Orientation']) {
                        case 8:
                            $degree = 90;
                            break;
                        case 3:
                            $degree = 180;
                            break;
                        case 6:
                            $degree = -90;
                            break;
                    }

                    // 세로사진의 경우 가로, 세로 값 바꿈
                    if($degree == 90 || $degree == -90) {
                        $tmp = $size;
                        $size[0] = $tmp[1];
                        $size[1] = $tmp[0];
                    }
                }
            }
            // 썸네일 높이
            $thumbHeight = round(($thumbWidth * $size[1]) / $size[0]);
            $img = $img->resize($thumbWidth, $thumbHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($thumbFilePath);
        }

        $thumbSize = getimagesize($thumbFilePath);
        $thumbSize = array_add($thumbSize, 'name', 'thumb-'. $imageName);
        // 썸네일 정보의 바로 사용가능한 width와 height에는 원본 width와 height를 넣는다.
        $thumbSize[0] = $size[0];
        $thumbSize[1] = $size[1];

        return $thumbSize;
    }

}
