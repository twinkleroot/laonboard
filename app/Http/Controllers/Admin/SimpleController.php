<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SimpleController extends Controller
{
    // phpinfo()
    public function phpinfo()
    {
        return phpinfo();
    }

    // 부가서비스
    public function extraService()
    {
        return view('admin.extra_service');
    }
}
