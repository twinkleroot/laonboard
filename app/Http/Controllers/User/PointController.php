<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Point;
use App\Config;

class PointController extends Controller
{

    public $pointModel;

    public function __construct(Point $point)
    {
        $this->middleware('level:2');

        $this->pointModel = $point;
    }

    public function index($id)
    {
        $params = $this->pointModel->getPointList($id);

        return view('user.point', $params);
    }
}
