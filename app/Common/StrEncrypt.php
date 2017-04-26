<?php

namespace App\Common;

// 문자열 암복호화
class StrEncrypt
{
    public $salt;
    public $length;

    function __construct($salt='')
    {
        if(!$salt)
            $this->salt = md5(config('database.connections.mysql.password'));
        else
            $this->salt = $salt;

        $this->length = strlen($this->salt);
    }

    // 암호화
    function encrypt($str)
    {
        $length = strlen($str);
        $result = '';

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    // 복호화
    function decrypt($str) {
        $result = '';
        $str    = base64_decode($str);
        $length = strlen($str);

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}
