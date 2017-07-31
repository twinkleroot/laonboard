<?php

function viewDefault($path, $params=[])
{
    $pathArr = explode('.', $path);
    $pathArr[1] = 'default';
    $defaultPath = implode('.', $pathArr);
    return view()->exists($path) ? view($path, $params) : view($defaultPath, $params);
}

function alert($message)
{
    return redirect(route('message'))->with('message', $message);
}

function alertClose($message)
{
    return redirect(route('message'))->with('message', $message)->with('popup', 1);
}

function alertRedirect($message, $redirect="/")
{
    return redirect(route('message'))->with('message', $message)->with('redirect', $redirect);
}

function alertErrorWithInput($message, $key)
{
    return redirect()->back()->withErrors([$key => $message])->withInput();
}

function confirm($message, $redirect)
{
    return redirect(route('confirm'))->with('message', $message)->with('redirect', $redirect);
}

// 스킨 목록을 가져온다.
function getSkins($type)
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

// 관리자에선 id, 커뮤니티에선 id_hashkey가 넘어오기 때문에 구별해서 user를 구해준다.
function getUser($id)
{
    $user;
    if(mb_strlen($id, 'utf-8') > 10) {  // 커뮤니티 쪽에서 들어올 때 user의 id가 아닌 id_hashKey가 넘어온다.
        $user = \App\User::where('id_hashkey', $id)->first();
    } else {
        $user = \App\User::find($id);
    }

    return $user ? : new \App\User();
}

// Text 형식으로 변환
function convertText($str, $html=0, $restore=false)
{
    $source[] = "<";
    $source[] = ">";
    $source[] = "\"";
    $source[] = "\'";

    $target[] = "&lt;";
    $target[] = "&gt;";
    $target[] = "&#034;";
    $target[] = "&#039;";

    if($restore) {
        $str = str_replace($target, $source, $str);
    }

    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = htmlSymbol($str);
    }

    if ($html) {
        $source[] = "\n";
        $target[] = "<br>";
    }

    return str_replace($source, $target, $str);
}

function htmlSymbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

// 글 내용 변환
function convertContent($content, $html)
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
        $content = htmlSymbol($content);

        // 공백 처리
        $content = str_replace("  ", "&nbsp; ", $content);
        $content = str_replace("\n ", "\n&nbsp;", $content);

        $content = convertText($content, 1);
        $content = urlAutoLink($content);
    }

    return $content;
}

function urlAutoLink($str)
{
    $config = cache("config.board");

    $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
    $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"{$config->linkTarget}\">\\2</A>", $str);
    $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"{$config->linkTarget}\">\\2</A>", $str);
    $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
    $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

    return $str;
}

// 문자열 자리수로 자르기(charset = 'utf-8')
function cutString($str, $len, $suffix="…")
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

function searchKeyword($keyword, $subject)
{
    // 문자앞에 \ 를 붙입니다.
    $src = array('/', '|');
    $dst = array('\/', '\|');

    if( !is_array($keyword) ) {
        if (!trim($keyword)) return $subject;

        // 검색어 전체를 공란으로 나눈다
        $s = explode(' ', $keyword);
    } else {
        $s = $keyword;
    }

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

// 검색어 특수문자 제거
function getSearchString($keyword)
{
    $pattern = array();
    $pattern[] = '#\.*/+#';
    $pattern[] = '#\\\*#';
    $pattern[] = '#\.{2,}#';
    $pattern[] = '#[/\'\"%=*\#\(\)\|\+\&\!\$~\{\}\[\]`;:\?\^\,]+#';

    $replace = array();
    $replace[] = '';
    $replace[] = '';
    $replace[] = '.';
    $replace[] = '';

    return preg_replace($pattern, $replace, $keyword);
}

// 올바르지 않은 코드가 글 내용에 다수 들어가 있는지 검사
function checkIncorrectContent($request)
{
    if (substr_count($request->content, '&#') > 50) {
        return false;
    }
    return true;
}

// 서버에서 지정한 Post의 최대 크기 검사
function checkPostMaxSize($request)
{
    if (empty($_POST)) {
        return false;
    }
    return true;
}

// 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
function checkAdminAboutNotice($request)
{
    if ( !session()->get('admin') && $request->has('notice') ) {
        return false;
    }
    return true;
}

// 파일 사이즈 구하기
function getFileSize($size)
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

// 입력 안된 필드( == null )는 입력값에서 제외.
function exceptNullData($data)
{
    foreach($data as $key => $value) {
        if(is_null($value)) {
            $data = array_except($data, $key);
        }
    }
    return $data;
}

// UTF-8 문자열 자르기
// 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8Strcut($str, $size, $suffix='...' )
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

// 관리 권한 설정 데이터를 가져온다.
function getManageAuthModel($menuCode)
{
    $manageAuth = \App\Admin\ManageAuth::
        where([
            'user_id' => auth()->user()->id,
            'menu' => $menuCode[0],
        ])
        ->where('auth', 'like', '%'. $menuCode[1]. '%')
        ->first();

    return $manageAuth;
}

// 게시판 캐시 삭제
function deleteCache($base, $boardTableName)
{
    $cacheName = $base. '-'. $boardTableName;
    cache()->forget($cacheName);
}

function getViewThumbnail($board, $imageName, $folder, $type="view")
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
    $height = round(($thumbWidth * $size[1]) / $size[0]);
    $files = explode('.', $imageName);
    $postfix;
    if($type == 'list') {	// 글 목록에서 썸네일을 필요로 할 때 (ex - 갤러리 게시판)
        $postfix = $board->gallery_height. '.'. $files[1];
    } else {
        $postfix = $thumbWidth. 'X'. $height. '.'. $files[1];
    }
    $thumbFilePath = $imgPath. '/thumb-'. $files[0]. '_'. $postfix;

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
        $thumbHeight = round(($thumbWidth * $size[1]) / $size[0]) > $board->gallery_height ? round(($thumbWidth * $size[1]) / $size[0]) : $board->gallery_height;
        $img = $img
            ->resize($thumbWidth, $thumbHeight, function ($constraint) {
                    $constraint->aspectRatio();
                })
            ->save($thumbFilePath);
    }

    $thumbSize = getimagesize($thumbFilePath);
    $thumbSize = array_add($thumbSize, 'name', basename($thumbFilePath));
    // 썸네일 정보의 바로 사용가능한 width와 height에는 원본 width와 height를 넣는다.
    $thumbSize[0] = $size[0];
    $thumbSize[1] = $size[1];

    return $thumbSize;
}

// 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function hyphenHpNumber($hp)
{
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}

// XSS 관련 태그 제거
function cleanXssTags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

// 이메일 주소 추출
function getEmailAddress($email)
{
    preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);

    return count($matches) > 0 ? $matches[0] : '';
}
