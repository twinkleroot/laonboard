<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Config;
use App\User;

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

        return viewDefault("user.{$this->skin}.inform", $params);
    }

    // 회원 알림 읽음 표시
    public function markAsRead(Request $request, $ids)
    {
        $this->userModel->markAsReadInforms($ids);

        return redirect(route('user.inform'));
    }

    // 회원 알림 내역 삭제
    public function destroy(Request $request, $ids=null)
    {
        $this->userModel->destroyInforms($ids);

        return redirect(route('user.inform'));
    }

}
