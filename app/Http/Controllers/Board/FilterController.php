<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Filter;

class FilterController extends Controller
{
    // 제목과 내용에 금지단어가 있는지 검사
    public function filter(Request $request)
    {
        $filter = new Filter;
        return $filter->filter($request);
    }
}
