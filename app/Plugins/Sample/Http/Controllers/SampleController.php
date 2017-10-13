<?php

namespace App\Plugins\Sample\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SampleController extends Controller
{
	public function index(Request $request)
	{
		return view('plugin:sample::index	');
	}
}