<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Popup;

class MainController extends Controller
{
    public $popup;

    public function __construct(Popup $popup)
    {
        $this->popup = $popup;
    }

    // 홈페이지 메인
    public function index(Request $request)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $params['popups'] = $this->popup->getPopupData();

        return viewDefault("$theme.main", $params);
    }

}
