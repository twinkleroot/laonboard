<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Models\User;

class InformsController extends Controller
{
    public $config;
    public $skin;
    public $userModel;

    public function __construct(Config $config, User $userModel)
    {
        $this->config = cache("config.join") ? : json_decode(Config::where('name', 'config.join')->first()->vars);
        $this->skin = $this->config->skin ? : 'default';
        $this->userModel = $userModel;
    }

    // 회원 알림 내역
    public function index(Request $request)
    {
        $informs = $this->userModel->getInforms($request);
        $params = [
            'informs' => $informs
        ];
        $theme = cache('config.theme')->name ? : 'default';

        return viewDefault("$theme.users.{$this->skin}.inform", $params);
    }

    // 회원 알림 읽음 표시 (읽음 표시 버튼 클릭)
    public function markAsRead(Request $request)
    {
        $this->userModel->markAsReadInforms($request->ids);

        return redirect(route('user.inform'));
    }

    // 회원 알림 내역 삭제
    public function destroy(Request $request)
    {
        $this->userModel->destroyInforms($request);

        return redirect(route('user.inform'));
    }

    // 회원 알림 읽음 표시 (ajax)
    public function markAsReadOne(Request $request)
    {
        return $this->userModel->markAsReadOne($request->id);
    }

}
