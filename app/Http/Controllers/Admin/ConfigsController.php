<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Config;

class ConfigsController extends Controller
{
    public $configModel;

    public function __construct(Config $configModel)
    {
        $this->configModel = $configModel;
    }

    public function index()
    {
        $params = $this->configModel->getConfigIndexParams();

        return view("admin.configs.basic", $params);
    }

    public function update(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        $data = $request->all();
        $message = '';

        if($this->configModel->updateConfig($data)) {
            $message = '기본환경설정 변경이 완료되었습니다.';
        } else {
            $message = '기본환경설정 변경에 실패하였습니다.';
        }
        return redirect(route('admin.config'))->with('message', $message);
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            // 홈페이지 기본 환경 설정
            'title' => 'bail|required|alpha_dash',
            'superAdmin' => 'bail|required|email',
            'usePoint' => 'bail|numeric|nullable',
            'loginPoint' => 'bail|numeric|required',
            'memoSendPoint' => 'bail|numeric|required',
            'openDate' => 'bail|numeric|required',
            'newDel' => 'bail|numeric|required',
            'memoDel' => 'bail|numeric|required',
            'newRows' => 'bail|numeric|required',
            'pageRows' => 'bail|numeric|required',
            // 'writePages' => 'bail|numeric|required',
            'newSkin' => 'bail|required',
            'searchSkin' => 'bail|required',
            'useCopyLog' => 'bail|numeric|nullable',
            'pointTerm' => 'bail|numeric|required',
            // 게시판 기본
            'linkTarget' => 'bail|required|regex:/^[a-z_]+$/',
            'readPoint' => 'bail|numeric|required',
            'writePoint' => 'bail|numeric|required',
            'commentPoint' => 'bail|numeric|required',
            'downloadPoint' => 'bail|numeric|required',
            // 'searchPart' => 'bail|numeric|required',
            'imageExtension' => 'bail|nullable',
            'flashExtension' => 'bail|nullable',
            'movieExtension' => 'bail|nullable',
            // 회원 가입
            'userSkin' => 'bail|alpha_dash|required',
            'nickDate' => 'bail|numeric|required',
            'name' => 'bail|numeric|required',
            'homepage' => 'bail|numeric|required',
            'tel' => 'bail|numeric|required',
            'hp' => 'bail|numeric|required',
            'addr' => 'bail|numeric|required',
            'signature' => 'bail|numeric|required',
            'profile' => 'bail|numeric|required',
            'joinLevel' => 'bail|numeric|required',
            'joinPoint' => 'bail|numeric|required',
            'leaveDay' => 'bail|numeric|required',
            'useMemberIcon' => 'bail|numeric|required',
            'iconLevel' => 'bail|numeric|required',
            'memberIconSize' => 'bail|numeric|required',
            'memberIconWidth' => 'bail|numeric|required',
            'memberIconHeight' => 'bail|numeric|required',
            'recommend' => 'bail|numeric|required',
            'recommendPoint' => 'bail|numeric|required',
            'passwordPolicyDigits' => 'bail|numeric|required',
            'passwordPolicySpecial' => 'bail|numeric|nullable',
            'passwordPolicyUpper' => 'bail|numeric|nullable',
            'passwordPolicyNumber' => 'bail|numeric|nullable',
            // 기본 메일 환경 설정
            'emailUse' => 'bail|numeric|nullable',
            'adminEmail' => 'bail|email|nullable',
            'adminEmailName' => 'bail|nullable|alpha_dash',
            'emailCertify' => 'bail|numeric|nullable',
            'formmailIsMember' => 'bail|numeric|nullable',
            // 게시판 글 작성 시 메일 설정
            'emailWriteSuperAdmin' => 'bail|numeric|nullable',
            'emailWriteGroupAdmin' => 'bail|numeric|nullable',
            'emailWriteBoardAdmin' => 'bail|numeric|nullable',
            'emailWriter' => 'bail|numeric|nullable',
            'emailAllCommenter' => 'bail|numeric|nullable',
            // 회원가입 시 메일 설정
            'emailJoinSuperAdmin' => 'bail|numeric|nullable',
            'emailJoinUser' => 'bail|numeric|nullable',
            // SNS 키 설정
            'kakaoKey' => 'bail|alpha_dash|nullable',
            'kakaoSecret' => 'bail|alpha_dash|nullable',
            'kakaoRedirect' => 'bail|regex:'. config('laon.URL_REGEX'). '|nullable',
            'naverKey' => 'bail|alpha_dash|nullable',
            'naverSecret' => 'bail|alpha_dash|nullable',
            'naverRedirect' => 'bail|regex:'. config('laon.URL_REGEX'). '|nullable',
            'facebookKey' => 'bail|alpha_dash|nullable',
            'facebookSecret' => 'bail|alpha_dash|nullable',
            'facebookRedirect' => 'bail|regex:'. config('laon.URL_REGEX'). '|nullable',
            'googleSecret' => 'bail|alpha_dash|nullable',
            'googleRedirect' => 'bail|regex:'. config('laon.URL_REGEX'). '|nullable',
            'googleRecaptchaClient' => 'bail|alpha_dash|nullable',
            'googleRecaptchaServer' => 'bail|alpha_dash|nullable',
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            // 홈페이지 기본 환경 설정
            'title.required' => '홈페이지 제목을 입력해 주세요.',
            'title.alpha_dash' => '홈페이지 제목은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'superAdmin.required' => '최고관리자를 선택해 주세요.',
            'superAdmin.email' => '최고관리자에 올바른 Email양식으로 입력해 주세요.',
            'usePoint.numeric' => '포인트사용에는 숫자만 들어갈 수 있습니다.',
            'loginPoint.required' => '로그인시 포인트를 입력해 주세요.',
            'loginPoint.numeric' => '로그인시 포인트에는 숫자만 들어갈 수 있습니다.',
            'memoSendPoint.required' => '쪽지보낼시 차감 포인트를 입력해 주세요.',
            'memoSendPoint.numeric' => '쪽지보낼시 차감 포인트에는 숫자만 들어갈 수 있습니다.',
            'openDate.required' => '정보공개 수정를 입력해 주세요.',
            'openDate.numeric' => '정보공개 수정에는 숫자만 들어갈 수 있습니다.',
            'newDel.required' => '최근게시물 삭제를 입력해 주세요.',
            'newDel.numeric' => '최근게시물 삭제에는 숫자만 들어갈 수 있습니다.',
            'memoDel.required' => '쪽지 삭제를 입력해 주세요.',
            'memoDel.numeric' => '쪽지 삭제에는 숫자만 들어갈 수 있습니다.',
            'newRows.required' => '최근게시물 라인수를 입력해 주세요.',
            'newRows.numeric' => '최근게시물 라인수에는 숫자만 들어갈 수 있습니다.',
            'pageRows.required' => '한 페이지당 라인수를 입력해 주세요.',
            'pageRows.numeric' => '한 페이지당 라인수에는 숫자만 들어갈 수 있습니다.',
            // 'writePages.required' => '페이지 표시 수를 입력해 주세요.',
            // 'writePages.numeric' => '페이지 표시 수에는 숫자만 들어갈 수 있습니다.',
            'newSkin.required' => '최근게시물 스킨을 선택해 주세요.',
            'searchSkin.required' => '검색 스킨을 선택해 주세요.',
            'useCopyLog.numeric' => '복사, 이동시 로그에는 숫자만 들어갈 수 있습니다.',
            'pointTerm.required' => '포인트 유효기간을 입력해 주세요.',
            'pointTerm.numeric' => '포인트 유효기간에는 숫자만 들어갈 수 있습니다.',
            // 게시판 기본
            'linkTarget.required' => '새창 링크를 선택해 주세요.',
            'linkTarget.regex' => '새창 링크에는 영어 소문자와 언더스코어(_)만 포함될 수 있습니다.',
            'readPoint.required' => '글읽기 포인트를 입력해 주세요.',
            'readPoint.numeric' => '글읽기 포인트에는 숫자만 들어갈 수 있습니다.',
            'writePoint.required' => '글쓰기 포인트를 입력해 주세요.',
            'writePoint.numeric' => '글쓰기 포인트에는 숫자만 들어갈 수 있습니다.',
            'commentPoint.required' => '댓글쓰기 포인트를 입력해 주세요.',
            'commentPoint.numeric' => '댓글쓰기 포인트에는 숫자만 들어갈 수 있습니다.',
            'downloadPoint.required' => '다운로드 포인트를 입력해 주세요.',
            'downloadPoint.numeric' => '다운로드 포인트에는 숫자만 들어갈 수 있습니다.',
            //'searchPart.numeric' => '검색 단위에는 숫자만 들어갈 수 있습니다.',
            // 회원 가입
            'userSkin.required' => '회원 스킨을 선택해 주세요.',
            'userSkin.alpha_dash' => '회원 스킨은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'nickDate.required' => '닉네임 수정을 입력해 주세요.',
            'nickDate.numeric' => '닉네임 수정에는 숫자만 들어갈 수 있습니다.',
            'name.required' => '이름 입력을 선택해 주세요.',
            'name.numeric' => '이름 입력에는 숫자만 들어갈 수 있습니다.',
            'homepage.required' => '홈페이지 입력을 선택해 주세요.',
            'homepage.numeric' => '홈페이지 입력에는 숫자만 들어갈 수 있습니다.',
            'tel.required' => '전화번호 입력을 선택해 주세요.',
            'tel.numeric' => '전화번호 입력에는 숫자만 들어갈 수 있습니다.',
            'hp.required' => '휴대폰번호 입력을 선택해 주세요.',
            'hp.numeric' => '휴대폰번호 입력에는 숫자만 들어갈 수 있습니다.',
            'addr.required' => '주소 입력을 선택해 주세요.',
            'addr.numeric' => '주소 입력에는 숫자만 들어갈 수 있습니다.',
            'signature.required' => '서명 입력을 선택해 주세요.',
            'signature.numeric' => '서명 입력에는 숫자만 들어갈 수 있습니다.',
            'profile.required' => '자기소개 입력을 선택해 주세요.',
            'profile.numeric' => '자기소개 입력에는 숫자만 들어갈 수 있습니다.',
            'joinLevel.required' => '회원가입시 권한을 선택해 주세요.',
            'joinLevel.numeric' => '회원가입시 권한에는 숫자만 들어갈 수 있습니다.',
            'joinPoint.required' => '회원가입시 지급 포인트를 입력해 주세요.',
            'joinPoint.numeric' => '회원가입시 지급 포인트에는 숫자만 들어갈 수 있습니다.',
            'leaveDay.required' => '회원탈퇴후 삭제일을 입력해 주세요.',
            'leaveDay.numeric' => '회원탈퇴후 삭제일에는 숫자만 들어갈 수 있습니다.',
            'useMemberIcon.required' => '회원아이콘 사용을 선택해 주세요.',
            'useMemberIcon.numeric' => '회원아이콘 사용에는 숫자만 들어갈 수 있습니다.',
            'iconLevel.required' => '아이콘 업로드 권한을 선택해 주세요.',
            'iconLevel.numeric' => '아이콘 업로드 권한에는 숫자만 들어갈 수 있습니다.',
            'memberIconSize.required' => '회원아이콘 용량을 입력해 주세요.',
            'memberIconSize.numeric' => '회원아이콘 용량에는 숫자만 들어갈 수 있습니다.',
            'memberIconWidth.required' => '회원아이콘 사이즈(가로)를 입력해 주세요.',
            'memberIconWidth.numeric' => '회원아이콘 사이즈(가로)에는 숫자만 들어갈 수 있습니다.',
            'memberIconHeight.required' => '회원아이콘 사이즈(세로)를 입력해 주세요.',
            'memberIconHeight.numeric' => '회원아이콘 사이즈(세로)에는 숫자만 들어갈 수 있습니다.',
            'recommend.required' => '추천인 제도를 선택해 주세요.',
            'recommend.numeric' => '추천인 제도에는 숫자만 들어갈 수 있습니다.',
            'recommendPoint.required' => '추천인 지급 포인트를 입력해 주세요.',
            'recommendPoint.numeric' => '추천인 지급 포인트에는 숫자만 들어갈 수 있습니다.',
            'passwordPolicyDigits.required' => '비밀번호 조합 정책을 입력해 주세요.',
            'passwordPolicySpecial.numeric' => '특수문자 하나 이상에는 숫자만 들어갈 수 있습니다.',
            'passwordPolicyUpper.numeric' => '대문자 하나 이상에는 숫자만 들어갈 수 있습니다.',
            'passwordPolicyNumber.numeric' => '숫자 하나 이상에는 숫자만 들어갈 수 있습니다.',
            // 기본 메일 환경 설정
            'emailUse.numeric' => '메일발송 사용에는 숫자만 들어갈 수 있습니다.',
            'adminEmail.email' => '관리자 메일 주소에 올바른 Email양식으로 입력해 주세요.',
            'adminEmailName.alpha_dash' => '관리자 메일 발송이름은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'emailCertify.numeric' => '이메일 인증 사용에는 숫자만 들어갈 수 있습니다.',
            'formmailIsMember.numeric' => '폼메일 사용 여부에는 숫자만 들어갈 수 있습니다.',
            // 게시판 글 작성 시 메일 설정
            'emailWriteSuperAdmin.numeric' => '게시판 글 작성시 최고관리자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            'emailWriteGroupAdmin.numeric' => '게시판 글 작성시 그룹관리자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            'emailWriteBoardAdmin.numeric' => '게시판 글 작성시 게시판관리자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            'emailWriter.numeric' => '게시판 글 작성시 원글작성자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            'emailAllCommenter.numeric' => '게시판 글 작성시 댓글작성자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            // 회원가입 시 메일 설정
            'emailJoinSuperAdmin.numeric' => '회원가입시 최고관리자에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            'emailJoinUser.numeric' => '회원가입시 가입한 회원에게 메일 발송에는 숫자만 들어갈 수 있습니다.',
            // SNS 키 설정
            'kakaoKey.alpha_dash' => '카카오 Key는 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'kakaoSecret.alpha_dash' => '카카오 Secret은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'kakaoRedirect.regex' => '카카오 Redirect URI에 올바른 url 형식으로 입력해 주세요.',
            'naverKey.alpha_dash' => '네이버 Key는 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'naverSecret.alpha_dash' => '네이버 Secret은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'naverRedirect.regex' => '네이버 Redirect URI에 올바른 url 형식으로 입력해 주세요.',
            'facebookKey.alpha_dash' => '페이스북 Key는 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'facebookSecret.alpha_dash' => '페이스북 Secret은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'facebookRedirect.regex' => '페이스북  Redirect URI에 올바른 url 형식으로 입력해 주세요.',
            'googleSecret.alpha_dash' => '구글 Secret은 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'googleRedirect.regex' => '구글 Redirect URI에 올바른 url 형식으로 입력해 주세요.',
            'googleRecaptchaClient.alpha_dash' => '구글 Invisible Recaptcha Key 클라이언트는 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
            'googleRecaptchaServer.alpha_dash' => '구글 Invisible Recaptcha Key 서버는 문자, 숫자, 대쉬(-), 언더스코어(_)만 포함할 수 있습니다.',
        ];
    }
}
