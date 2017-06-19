<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main;

class HomeController extends Controller
{
    public $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function index()
    {
        $mainContents = $this->main->getMainContents('default');    // 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        $skin = 'default';   // default를 가리킴, 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        return view($skin, $mainContents);
    }
}
