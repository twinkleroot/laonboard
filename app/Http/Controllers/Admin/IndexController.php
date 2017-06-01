<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cache;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('level:10');
    }

    public function index()
    {
        $params = [
            'config' => Cache::get("config.homepage"),
        ];

        return view('admin.index', $params);
    }
}
