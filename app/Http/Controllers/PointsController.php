<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;

class PointsController extends Controller
{
    public $pointModel;

    public function __construct(Point $point)
    {
        $this->pointModel = $point;
    }

    // 회원 포인트 내역
    public function history($id)
    {
        if(!auth()->check() || auth()->user()->id_hashkey != $id) {
            return alert('다른 회원의 포인트 내역을 조회할 수 없습니다.');
        }
        $params = $this->pointModel->getPointList(auth()->user()->id);
        $theme = cache('config.theme')->name;

        return viewDefault("$theme.points.history", $params);
    }
}
