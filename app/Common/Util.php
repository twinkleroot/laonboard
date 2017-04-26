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
}
