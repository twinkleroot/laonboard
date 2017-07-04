<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReCaptcha extends Model
{
    /*
    * 구글 리캡챠 서버쪽 검사
    *
    * @return boolean
    */
    public static function reCaptcha(Request $request) {
        $url = 'https://www.google.com/recaptcha/api/siteverify'
                . '?secret=6LfctScUAAAAAJAjgAtoT-E9TO4C4zDqzFQXBF54&response='
                . $request['g-recaptcha-response'];
        $flag = json_decode(file_get_contents($url));

        return $flag->success;
    }

}
