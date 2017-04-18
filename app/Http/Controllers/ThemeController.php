<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

class ThemeController extends Controller
{
    public function index()
    {
        return view('theme');
    }

    // 메뉴 테스트
    public function menuTest()
    {
        $menus = Menu::where('use', 1)
                    ->whereRaw('length(code) = 2')
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

        $subMenus = [];
        for($i=0; $i<count($menus); $i++) {
            $subMenus[$i] = Menu::where('use', 1)
                    ->whereRaw('length(code) = 4')
                    ->whereRaw('substring(code, 1, 2)=' . $menus[$i]['code'])
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
        }

        // dump($menus);
        // dump($subMenus);
        // dump(count($subMenus[0]));
        // dump(count($subMenus[1]));
        // dump(count($subMenus[2]));
        // dd(count($subMenus[3]));

        return view('test/menu', [
            'menus' => $menus,
            'subMenus' => $subMenus,
        ]);
    }
}
