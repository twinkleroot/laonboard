<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Config;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('level:10');
    }

    public function index()
    {
        $params = [
            'config' => Config::getConfig('config.homepage'),
        ];

        return view('admin.index', $params);
    }
}
