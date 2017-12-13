<?php

// php 7.2 에서 count(arg)에 arg가 null 이면 오류나는 문제 해결을 위해 사용
if(! function_exists('isNotNullCount')) {
    function notNullCount($countable)
    {
        if(isset($countable)) {
            if(is_object($countable) || is_array($countable)) {
                return count($countable);
            }
        }

        return 0;
    }
}

// 설정 값 추가
if(! function_exists('addCustomConfig')) {
    function addCustomConfig($name, $data)
    {
        $config = '';
        if(cache("config.$name")) {
            $config = cache("config.$name");
        } else if($nameConfig = App\Models\Config::where('name', "config.$name")->first()) {
            $config = json_decode($nameConfig->vars);
            Cache::forever("config.$name", $config);
        }

        if(!$config) {
            App\Models\Config::insert([
                'name' => "config.$name",
                'vars' => json_encode($data),
            ]);

            $config = App\Models\Config::where('name', "config.$name")->first();

            if($config) {
                Cache::forever("config.$name", json_decode($config->vars));
            }
        }
    }
}
// 배열을 값을 기준으로 정렬
if(! function_exists('sortArray')) {
    function sortArray($arr, $key)
    {
        return array_sort($arr, function($value) use($key) {
            return $value[$key];
        });
    }
}

// laravel app에 event 등록
if(! function_exists('mergeEvent')) {
    function mergeEvent($path, $key, $change=0)
    {
        $config = app()['config']->get($key,[]);

        foreach(require $path as $pathKey => $value) {
            if(isset($config[$pathKey])) {
                if($change) {
                    // event change - if module off, back to default event.
                    $config[$pathKey] = $value;
                } else {
                    // array_add
                    $config[$pathKey] = array_add($config[$pathKey], key($value), $value[key($value)]);
                }
                app()['config']->set($key, $config);
            } else {
                // array_merge
                app()['config']->set($key, array_merge(require $path, $config));
            }
        }
    }
}

// event를 이용한 hook 기능
if(! function_exists('fireEvent')) {
    function fireEvent($tag, ...$params)   // $args에 타입별로 한개씩만 넣기
    {
        $classes = config("event.$tag");

        if(notNullCount($classes) > 0) {
            // priority 오름차순으로 이벤트 실행 순서 정렬하기
            $classes = array_sort($classes, function($value) {
                return $value['priority'];
            });

            foreach($classes as $key => $value) {
                $namespace = eventNamespace($key, $value);
                if(class_exists($namespace) && $value['use']) {
                    event(new $namespace(request(), ...$params));
                }
            }
        }
    }
}

// module event namespace 조합
if(! function_exists('eventNamespace')) {
    function eventNamespace($key, $value)
    {
        $first = 'Modules';
        $second = $value['module'];
        $third = 'Events';
        $fourth = studly_case($key);
        if(strtolower($value['module']) == 'laonboard') {
            $first = 'App';
            $second = 'Modules';
        }

        return "$first\\$second\\$third\\$fourth";
    }
}

// front sideview 경로를 return 한다.
if(! function_exists('cookingMenuSubject')) {
    function cookingMenuSubject($result, $id)
    {
        $menu = App\Models\Menu::where('name', $result->subject)->whereRaw("INSTR(link, '$id')")->first();
        if ($menu) {
            $result->subject .= ' (이미 추가 된 메뉴)';
        }
        return $result;
    }
}

// front sideview 경로를 return 한다.
if(! function_exists('getFrontSideview')) {
    function getFrontSideview()
    {
        $view = 'themes.'. cache('config.theme')->name. '.sideviews.front';
        $view = view()->exists($view) ? $view : 'themes.default.sideviews.front';
        return $view;
    }
}

// 메인에 노출하는 최근 게시물 형태로 가공
if(! function_exists('getLatestWrites')) {
    function getLatestWrites($boards, $pageRows, $titleLength)
    {
        $latests = array();
        $cacheMinutes = 60;
        $i = 0;
        foreach($boards as $board) {
            $latests[$i] = Cache::remember('main-'. $board->table_name, $cacheMinutes, function() use($board, $pageRows) {
                return DB::table('write_'. $board->table_name)
                        ->where('is_comment', 0)
                        ->orderBy('num')
                        ->limit($pageRows)
                        ->get();
            });

            $latests[$i]->board_id = $board->id;
            $latests[$i]->board_subject = $board->subject;
            $latests[$i]->table_name = $board->table_name;
            $latests[$i]->new = $board->new;
            $latests[$i]->hot = $board->hot;
            $subjectLength = config('laon.subject_len.main') ? : 15;
            $write = $latests[$i]->first();
            foreach($latests[$i] as $write) {
                if($write) {
                    $write->subject = subjectLength($write->subject, $subjectLength);
                }
            }

            $i++;
        }

        return $latests;
    }
}

// 쿠키 가져오기
if(! function_exists('pickupCookie')) {
    function pickupCookie($name)
    {
        return base64_decode(Cookie::get(md5($name)));
    }
}

// 쿠키 생성
if(! function_exists('makeCookie')) {
    function makeCookie($name, $value, $minutes, $path=null, $domain=null)
    {
        return Cookie::make(md5($name), base64_encode($value), $minutes, $path, $domain);
    }
}

// 모바일 환경인지
if(! function_exists('isMobile')) {
    function isMobile()
    {
        return preg_match('/phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|android|sony/i', request()->server('HTTP_USER_AGENT'));
    }
}

// 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
if(! function_exists('includeImagePathByEditor')) {
    function includeImagePathByEditor($board, $content)
    {
        // 에디터로 업로드한 이미지 경로를 추출한다.
        $imgPattern = "/<img[^>]*src=[\"']?\/storage([^>\"']+)[\"']?[^>]*>/i";

        preg_match_all($imgPattern, $content, $matches);

        for($i=0; $i<notNullCount($matches[1]); $i++) {
            // 썸네일 만들기
            $imageFileInfo = getViewThumbnail($board, basename($matches[1][$i]), 'editor');
            if(!$imageFileInfo) {
                return $content;
            }

            $divImage1 = explode('.', $imageFileInfo['name']);
            $divImage2 = explode('_', $divImage1[0]);
            $realImageName = str_replace("thumb-", "", $divImage2[0]). '.'. last($divImage1);

            $html = "<a href='". route('image.original'). "?type=editor&amp;imageName=". $realImageName. "'"
                    . " class='viewOriginalImage' width='". $imageFileInfo[0]. "' height='". $imageFileInfo[1]. "' target='viewImage'>"
                    . "<img src='/storage/editor/". $imageFileInfo['name']. "' /></a>";
            // 글 내용에 이미지 원본보기 링크와 이미지경로를 넣어준다.
            $replacePattern = "/<img[^>]*src=[\"']?([^>\"']+){$realImageName}[\"']?[^>]*>/i";
            $content = preg_replace($replacePattern, $html, $content);
        }

        return $content;
    }
}

// Open Graph 이미지를 뽑아 낸다.
if(! function_exists('pullOutImage')) {
    function pullOutImage($content, $boardId=0, $writeId=0, $file=0)
    {
        $path = 'themes/laon/images/title_logo.gif';
        $flag = 0;
        // 글 내용에 포함된 이미지에서 이미지를 가져오기
        if($content) {
            $imgPattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/";
            preg_match($imgPattern, $content, $matches);
            if(isset($matches[1]) > 0) {
                $path = $matches[1];
                $flag = 1;
            }
        }
        // 첨부파일에서 이미지 가져오기
        if(!$flag && $file) {
            $boardModel = app()->tagged('board')[0];
            $board = $boardModel::getBoard($boardId);
            $boardFiles = App\Models\BoardFile::where([
                'board_id' => $boardId,
                'write_id' => $writeId
            ])->get();
            foreach ($boardFiles as $boardFile) {
                $boardFilePath = storage_path('app/public/'. $board->table_name. '/'. $boardFile->file);
                if(File::exists($boardFilePath)) {
                    $size = getimagesize($boardFilePath);
                    if($size) {
                        $path = '/storage/'.$board->table_name.'/'.$boardFile->file;
                        break;
                    }
                }
            }
        }

        return $path;
    }
}

if(! function_exists('ver_asset')) {
    function ver_asset($path)
    {
        return asset($path). '?ver='. config('laon.VER');
    }
}

if (! function_exists('mailer')) {
    // 메일 보내기 (파일 여러개 첨부 가능)
    // type : text=0, html=1, text+html=2
    function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
    {
        if ($type != 1) {
            $content = nl2br($content);
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(); // defaults to using php "mail()"
        if (config('mail.driver') == 'smtp') {
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = config('mail.host'); // SMTP server
            $mail->Port = config('mail.port');
        }

        $mail->CharSet = 'UTF-8';
        $mail->From = $fmail;
        $mail->FromName = $fname;
        $mail->Subject = $subject;
        $mail->AltBody = ""; // optional, comment out and test
        $mail->msgHTML($content);
        $mail->addAddress($to);

        if ($cc) {
            $mail->addCC($cc);
        }
        if ($bcc) {
            $mail->addBCC($bcc);
        }
        if ($file != "") {
            foreach ($file as $f) {
                $mail->addAttachment($f['path'], $f['name']);
            }
        }

        return $mail->send();
    }
}


// 관리자 데모인지 판별
if (! function_exists('isDemo')) {
    function isDemo()
    {
        if(config('demo.email') && config('demo.password')) {
            if(auth()->check()) {
                if(!auth()->user()->isSuperAdmin()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        return false;
    }
}

// 관리자 데모일 경우 데이터를 *로 표시
if (! function_exists('invisible')) {
    function invisible($data)
    {
        $result = '';
        for($i=0; $i<mb_strlen($data, 'UTF-8'); $i++) {
            $result .= '*';
        }
        return $result;
    }
}
// 포인트 부여
if (! function_exists('insertPoint')) {
    function insertPoint($userId, $point, $content='', $relTable='', $relEmail='', $relAction='', $expire=0)
    {
        $configHomepage = Cache::get("config.homepage");
        // 포인트 사용을 하지 않는다면 return
        if(!$configHomepage->usePoint) {
            return 0;
        }
        // 부여할 포인트가 없다면 업데이트 할 필요 없음
        if($point == 0) {
            return 0;
        }
        // 회원아이디가 없다면 업데이트 할 필요 없음
        if($userId == '') {
            return 0;
        }
        $user = App\Models\User::find($userId);
        if(is_null($user)) {
            return 0;
        }

        // 회원포인트
        $userPoint = getPointSum($userId);

        // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
        $existPoint = checkPoint($relTable, $relEmail, $relAction);
        if( !is_null($existPoint) ) {
            return 0;
        }
        // 포인트 건별 생성
        // 만료일 설정
        $expireDate = date('9999-12-31');
        if($configHomepage->pointTerm > 0) {
            if($expire > 0) {
                $expireDate = Carbon\Carbon::now()->addDays($expire-1)->toDateString();
            } else {
                $expireDate = Carbon\Carbon::now()->addDays($configHomepage->pointTerm-1)->toDateString();
            }
        }
        $pointExpired = 0;
        if($point < 0) {
            $pointExpired = 1;
            $expireDate = Carbon\Carbon::now()->toDateString();
        }

        App\Models\Point::insert([
                    'user_id' => $userId,
                    'datetime' => Carbon\Carbon::now(),
                    'content' => addslashes($content),
                    'point' => $point,
                    'use_point' => 0,
                    'user_point' => $userPoint + $point,
                    'expired' => $pointExpired,
                    'expire_date' => $expireDate,
                    'rel_table' => $relTable,
                    'rel_email' => $relEmail,
                    'rel_action' => $relAction,
        ]);
        // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
        if($point < 0) {
            insertUsePoint($userId, $point);
        }
        // User 테이블의 point 업데이트
        return App\Models\User::where('id', $userId)->increment('point', $point);
    }
}

// 포인트 삭제
if (! function_exists('deletePoint')) {
    function deletePoint($userId, $relTable, $relEmail, $relAction)
    {
        $result = 0;
        if($relTable || $relEmail || $relAction) {
            // 포인트 내역정보
            $point = App\Models\Point::where([
                'user_id' => $userId,
                'rel_table' => $relTable,
                'rel_email' => $relEmail,
                'rel_action' => $relAction,
            ])->first();

            if($point) {
                if($point->point < 0) {
                    $userId = $point->user_id;
                    $usePoint = abs($point->point);

                    deleteUsePoint($userId, $usePoint);
                } else {
                    if($point->use_point > 0) {
                        insertUsePoint($userId, $point->use_point, $point->id);
                    }
                }

                $result = App\Models\Point::where([
                    'user_id' => $userId,
                    'rel_table' => $relTable,
                    'rel_email' => $relEmail,
                    'rel_action' => $relAction,
                ])->delete();

                // user_point에 반영
                App\Models\Point::where('user_id', $userId)
                    ->where('id', '>', $point->id)
                    ->decrement('user_point', $point->point);

                // 포인트 내역의 합을 구하고
                $sumPoint = getPointSum($userId);

                // User의 포인트 업데이트
                $result = App\Models\User::where('id', $userId)->update(['point' => $sumPoint]);
            }
        }

        return $result;
    }
}

// 같은 건으로 포인트를 수령했는지 검사
if (! function_exists('checkPoint')) {
    function checkPoint($relTable, $relEmail, $relAction)
    {
        return App\Models\Point::where([
            'rel_table' => $relTable,
            'rel_email' => $relEmail,
            'rel_action' => $relAction,
        ])->first();
    }
}

// 유저별 포인트 합 구하기
if (! function_exists('getPointSum')) {
    function getPointSum($userId)
    {
        // 만료된 포인트 내역 처리
        if(cache('config.homepage')->pointTerm > 0) {
            $expirePoint = getExpirePoint($userId);
            if($expirePoint > 0) {
                $user = App\Models\User::find($userId);
                $content = '포인트 소멸';
                $point = $expirePoint * (-1);
                $pointUserPoint = $user->point + $point;
                App\Models\Point::insert([
                    'user_id' => $userId,
                    'datetime' => Carbon\Carbon::now(),
                    'content' => addslashes($content),
                    'point' => $point,
                    'use_point' => 0,
                    'user_point' => $pointUserPoint,
                    'expired' => 1,
                    'expire_date' => Carbon\Carbon::now()->toDateString(),
                    'rel_table' => '@expire',
                    'rel_email' => $userId,
                    'rel_action' => 'expire'.'-'.uniqid(''),
                ]);

                // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
                if($point < 0) {
                    insertUsePoint($userId, $point);
                }
            }
            // 유효기간이 있을 때 기간이 지난 포인트 expired 체크
            App\Models\Point::where('user_id', $userId)
                ->where('expired', '<>', 1)
                ->where('expire_date', '<>', '9999-12-31')
                ->where('expire_date', '<', Carbon\Carbon::now()->toDateString())
                ->update([ 'expired' => 1 ]);
        }

        // 포인트 합
        return App\Models\Point::where('user_id', $userId)->sum('point');
    }
}


// 글 삭제할 때 포인트 삭제
if (! function_exists('deleteWritePoint')) {
    function deleteWritePoint($writeModel, $boardId, $writeId)
    {
        $writeModel = app()->tagged('write')[0];
        $boardModel = app()->tagged('board')[0];
        $write = $writeModel::getWrite($boardId, $writeId);
        $board = $boardModel::getBoard($boardId);
        // 원글에서의 처리
        $deleteResult = 0;
        $insertResult = 0;
        if(!$write->is_comment) {
           // 포인트 삭제 및 사용 포인트 다시 부여
           $deleteResult = deletePoint($write->user_id, $board->table_name, $writeId, '쓰기');
           if($deleteResult == 0) {
               $insertResult = insertPoint($write->user_id, $board->write_point * (-1), $board->subject. ' '. $writeId. ' 글삭제');
           }
        } else {   // 댓글에서의 처리
           // 포인트 삭제 및 사용 포인트 다시 부여
           $deleteResult = deletePoint($write->user_id, $board->table_name, $writeId, '댓글');
           if($deleteResult == 0) {
               $insertResult = insertPoint($write->user_id, $board->write_point * (-1), $board->subject. ' '. $write->parent. '-'. $writeId. ' 댓글삭제');
           }
        }
    }
}

// 소멸 포인트
if (! function_exists('getExpirePoint')) {
    function getExpirePoint($userId)
    {
        if(cache('config.homepage')->pointTerm == 0) {
            return 0;
        }

        $point =
            App\Models\Point::selectRaw('sum(point - use_point) as sum_point')
            ->where([ 'user_id' => $userId, 'expired' => 0 ])
            ->where('expire_date', '<>', '9999-12-31')
            ->where('expire_date', '<', Carbon\Carbon::now()->toDateString())
            ->first();

        return $point->sum_point;
    }
}

// 사용 포인트 삭제
if (! function_exists('deleteUsePoint')) {
    function deleteUsePoint($userId, $usePoint)
    {
        $point1 = abs($usePoint);

        $query = App\Models\Point::where('user_id', $userId)
                    ->where('expired', '<>', 1)
                    ->where('use_point', '>', 0);
        if(cache('config.homepage')->pointTerm > 0) {
            $query = $query->orderBy('expire_date', 'desc')->orderBy('id', 'desc');
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        $points = $query->get();
        foreach($points as $point) {
            $point2 = $point->use_point;

            $expired = $point->expired;
            if($point->expired == 100 && ($point->expire_date == '9999-12-31' || $point->expire_date >= Carbon\Carbon::now()->toDateString()) ) {
                $expired = 0;
            }
            if($point2 > $point1) {
                App\Models\Point::where('id', $point->id)
                    ->decrement('use_point', $point1, ['expired' => $expired]);
                break;
            } else {
                App\Models\Point::where('id', $point->id)
                    ->update([
                        'use_point' => 0,
                        'expired' => $expired,
                    ]);
                $point1 -= $point2;
            }
        }
    }
}

// 만료 포인트 삭제
if (! function_exists('deleteExpirePoint')) {
    function deleteExpirePoint($userId, $usePoint)
    {
        $point1 = abs($usePoint);

        $points =
            App\Models\Point::where([
                'user_id' => $userId,
                'expired' => 1
            ])
            ->where('point', '>=', 0)
            ->where('use_point', '>', 0)
            ->orderBy('expire_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach($points as $point) {
            $point2 = $point->use_point;
            $expired = 0;
            $expireDate = '9999-12-31';
            if(cache('config.homepage')->pointTerm > 0) {
                $expireDate = Carbon\Carbon::now()->addDays($configHomepage->pointTerm-1)->toDateString();
            }
            if($point2 > $point1) {
                App\Models\Point::where('id', $point->id)
                    ->decrement('use_point', $point1, ['expired' => $expired, 'expire_date' => $expireDate]);
                break;
            } else {
                App\Models\Point::where('id', $point->id)
                    ->update([
                        'use_point' => 0,
                        'expired' => $expired,
                        'expire_date' => $expireDate,
                    ]);
                $point1 -= $point2;
            }
        }
    }
}

// 사용포인트 입력
if (! function_exists('insertUsePoint')) {
    function insertUsePoint($userId, $usePoint, $id='')
    {
        $point1 = abs($usePoint);

        $points = App\Models\Point::where('user_id', $userId)
                    ->where('id', '<>', $id)
                    ->where('expired', '=', 0)
                    ->where('use_point', '>', 0)
                    ->get();

        foreach($points as $point) {
            $point2 = $point->point;
            $point3 = $point->use_point;

            if(($point2 - $point3) > $point) {
                App\Models\Point::where('id', $id)->increment('use_point', $point1);
                break;
            } else {
                $point4 = $point2 - $point3;
                App\Models\Point::where('id', $id)->increment('use_point', $point4, ['expired' => 100]);
                $point1 -= $point4;
            }
        }
    }
}

if (! function_exists('viewDefault')) {
    function viewDefault($path, $params=[])
    {
        $path = 'themes.'. $path;
        $pathArr = explode('.', $path);
        $pathArr[1] = 'default';
        $defaultPath = implode('.', $pathArr);
        return view()->exists($path) ? view($path, $params) : view($defaultPath, $params);
    }
}

if (! function_exists('alert')) {
    function alert($message)
    {
        return redirect(route('message'))->with('message', $message);
    }
}

if (! function_exists('alertClose')) {
    function alertClose($message)
    {
        return redirect(route('message'))->with('message', $message)->with('popup', 1);
    }
}

if (! function_exists('alertRedirect')) {
    function alertRedirect($message, $redirect="/")
    {
        return redirect(route('message'))->with('message', $message)->with('redirect', $redirect);
    }
}

if (! function_exists('alertErrorWithInput')) {
    function alertErrorWithInput($message, $key)
    {
        return redirect()->back()->withErrors([$key => $message])->withInput();
    }
}

if (! function_exists('confirm')) {
    function confirm($message, $redirect)
    {
        return redirect(route('confirm'))->with('message', $message)->with('redirect', $redirect);
    }
}

if (! function_exists('getSkins')) {
    // 스킨 목록을 가져온다.
    function getSkins($type)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $path = resource_path("views/themes/$theme/$type");
        // $result = [];
        $result = ['' => '선택'];
        if(File::exists($path)) {
            $dirs = File::directories($path);
            foreach($dirs as $dir) {
                $result[basename($dir)] = basename($dir);
            }
        }

        if(notNullCount($result) == 1) {
            $path = resource_path("views/themes/default/$type");
            if(File::exists($path)) {
                $dirs = File::directories($path);
                foreach($dirs as $dir) {
                    $result[basename($dir)] = basename($dir);
                }
            }
        }

        return $result;
    }
}

if (! function_exists('getUser')) {
    // 관리자에선 id, 커뮤니티에선 id_hashkey가 넘어오기 때문에 구별해서 user를 구해준다.
    function getUser($id)
    {
        $user;
        if(mb_strlen($id, 'utf-8') > 10) {  // 커뮤니티 쪽에서 들어올 때 user의 id가 아닌 id_hashKey가 넘어온다.
            $user = App\Models\User::where('id_hashkey', $id)->first();
        } else {
            $user = App\Models\User::find($id);
        }

        return $user ? : new App\Models\User();
    }
}

if (! function_exists('convertText')) {
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
}

if (! function_exists('htmlSymbol')) {
    function htmlSymbol($str)
    {
        return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
    }
}

if (! function_exists('convertContent')) {
    // 글 내용 변환
    function convertContent($content, $html, $filter=true)
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

            if($filter) {
                $content = clean($content);
            }
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
}

if (! function_exists('urlAutoLink')) {
    function urlAutoLink($str)
    {
        $target = cache("config.board")->linkTarget;

        $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
        $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#!=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<a href=\"\\2\" target=\"$target\" rel=\"nofollow\">\\2</a>", $str);
        $str = preg_replace("/(^|[\"'\s(])?(www\.[^\"'\s()<]+)/i", "\\1<a href=\"http://\\2\" target=\"$target\" rel=\"nofollow\">\\2</a>", $str);
        $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
        $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

        return $str;
    }
}

if (! function_exists('cutString')) {
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
}

if (! function_exists('searchKeyword')) {
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
        for ($m=0; $m<notNullCount($s); $m++) {
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
}

if (! function_exists('getSearchString')) {
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
}

if (! function_exists('checkIncorrectContent')) {
    // 올바르지 않은 코드가 글 내용에 다수 들어가 있는지 검사
    function checkIncorrectContent($request)
    {
        if (substr_count($request->content, '&#') > 50) {
            return false;
        }
        return true;
    }
}

if (! function_exists('checkAdminAboutNotice')) {
    // 관리자가 아닌데 공지사항을 남기려 하는 경우가 있는지 검사
    function checkAdminAboutNotice($request)
    {
        if ( !session()->get('admin') && $request->filled('notice') ) {
            return false;
        }
        return true;
    }
}

if (! function_exists('getFileSize')) {
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
}

if (! function_exists('exceptNullData')) {
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
}

if (! function_exists('utf8Strcut')) {
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
}

if (! function_exists('getManageAuthModel')) {
    // 관리 권한 설정 데이터를 가져온다.
    function getManageAuthModel($menuCode)
    {
        $manageAuth = App\Models\ManageAuth::
            where([
                'user_id' => auth()->user()->id,
                'menu' => $menuCode[0],
            ])
            ->where('auth', 'like', '%'. $menuCode[1]. '%')
            ->first();

        return $manageAuth;
    }
}

if (! function_exists('deleteCache')) {
    // 게시판 캐시 삭제
    function deleteCache($base, $boardTableName)
    {
        $cacheName = $base. '-'. $boardTableName;
        cache()->forget($cacheName);
    }
}

if (! function_exists('getViewThumbnail')) {
    // 썸네일 만들기 (list, view 용도는 $type 파라미터로 구분)
    function getViewThumbnail($board, $imageName, $folder, $type="view")
    {
        $imgPath = storage_path('app/public/'. $folder);

        $imgPathAndFileName = $imgPath. '/'. $imageName;
        if(!File::exists($imgPathAndFileName)) {
            // return abort(500, '첨부파일이 존재하지 않습니다.');
            return false;
        }
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
        $files = explode('.', $imageName);
        $height = round(($thumbWidth * $size[1]) / $size[0]);
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
}

if (! function_exists('hyphenHpNumber')) {
    // 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
    function hyphenHpNumber($hp)
    {
        $hp = preg_replace("/[^0-9]/", "", $hp);
        return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
    }
}

if (! function_exists('cleanXssTags')) {
    // XSS 관련 태그 제거
    function cleanXssTags($str)
    {
        $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

        return $str;
    }
}

if (! function_exists('getEmailAddress')) {
    // 이메일 주소 추출
    function getEmailAddress($email)
    {
        preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);

        return notNullCount($matches) > 0 ? $matches[0] : '';
    }
}

if (! function_exists('subjectLength')) {
    // 글 제목 목록에서 설정값에 따라 자르기
    function subjectLength($subject, $length)
    {
        $result = $subject;
        if($subject && mb_strlen($subject, 'UTF-8') > $length) {
            $result = mb_substr($subject, 0, $length, 'UTF-8'). '...';
        }

        return $result;
    }
}

if (! function_exists('getIconFolderName')) {
    // 글 제목 목록에서 설정값에 따라 자르기
    function getIconFolderName($date)
    {
        $userCreated = new Carbon\Carbon($date);

        return $userCreated->format('Ym');
    }
}

if (! function_exists('getIconName')) {
    // 글 제목 목록에서 설정값에 따라 자르기
    function getIconName($id, $date)
    {
        return md5($id. $date);
    }
}
