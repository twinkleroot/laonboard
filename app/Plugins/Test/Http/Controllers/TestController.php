<?php

namespace App\Plugins\Test\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
	public function index(Request $request)
	{
		return view('plugin:test::index');
	}
}