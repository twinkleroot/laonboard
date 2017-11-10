<?php

namespace Modules\Certify\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HpCertifyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            \Modules\Certify\Events\AddCertFuncToRegister::class,
            __CLASS__ . '@addCertFuncToRegister'
        );
        $events->listen(
            \Modules\Certify\Events\AddRegisterScript::class,
            __CLASS__ . '@addRegisterScript'
        );
        $events->listen(
            \Modules\Certify\Events\AddNameAndHpToEditUserInfo::class,
            __CLASS__ . '@addNameAndHpToEditUserInfo'
        );
        $events->listen(
            \Modules\Certify\Events\AddEditScript::class,
            __CLASS__ . '@addEditScript'
        );
        $events->listen(
            \Modules\Certify\Events\AddCertTabToManageUserTab::class,
            __CLASS__ . '@addCertTabToManageUserTab'
        );
        $events->listen(
            \Modules\Certify\Events\AddCertConfigToManageUserForm::class,
            __CLASS__ . '@addCertConfigToManageUserForm'
        );
        $events->listen(
            \Modules\Certify\Events\AddCertConfigToManageBoardForm::class,
            __CLASS__ . '@addCertConfigToManageBoardForm'
        );
    }

    // 사용자 - 회원가입 양식에 본인 확인 추가
    public function addCertFuncToRegister(\Modules\Certify\Events\AddCertFuncToRegister $event)
    {
        // 본인확인 관련 세션 초기화
        session()->put("ss_cert_no", "");
        session()->put("ss_cert_hash", "");
        session()->put("ss_cert_type", "");

        echo view("modules.certify.register");
    }

    // 사용자 - 회원가입 양식 제출 전에 본인 확인 데이터 추가
    public function addRegisterScript(\Modules\Certify\Events\AddRegisterScript $event)
    {
        echo view("modules.certify.register_script");
    }

    // 사용자 - 회원정보수정 양식에 본인 확인 추가
    public function addNameAndHpToEditUserInfo(\Modules\Certify\Events\AddNameAndHpToEditUserInfo $event)
    {
        // 본인확인 관련 세션 초기화
        session()->put("ss_cert_no", "");
        session()->put("ss_cert_hash", "");
        session()->put("ss_cert_type", "");

        $params = [
            'user' => auth()->user(),
            'config' => cache('config.join')
        ];

        echo view("modules.certify.edit", $params);
    }

    // 사용자 - 회원가입 양식 제출 전에 본인 확인 데이터 추가
    public function addEditScript(\Modules\Certify\Events\AddEditScript $event)
    {
        echo view("modules.certify.edit_script");
    }

    // 관리자 - 회원 관리 양식의 탭에 본인 확인 탭 추가
    public function addCertTabToManageUserTab(\Modules\Certify\Events\AddCertTabToManageUserTab $event)
    {
        echo view("modules.certify.admin.user_form_tab");
    }

    // 관리자 - 회원 관리 양식에 본인 확인 추가
    public function addCertConfigToManageUserForm(\Modules\Certify\Events\AddCertConfigToManageUserForm $event)
    {
        $params = [
            'user' => \App\Models\User::find(request()->user),
        ];

        echo view("modules.certify.admin.user_form", $params);
    }

    // 게시판 관리 양식에 본인 확인 추가
    public function addCertConfigToManageBoardForm(\Modules\Certify\Events\AddCertConfigToManageBoardForm $event)
    {
        $segments = request()->segments();
        // ex) $segments = [ 'admin', 'boards', 'free', 'edit' ];

        $params = [
            'board' => \App\Models\Board::whereTableName($segments[2])->first(),
            'type' => $segments[3]
        ];

        echo view("modules.certify.admin.board_form", $params);
    }

}
