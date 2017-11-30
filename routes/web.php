<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| 커뮤니티
|--------------------------------------------------------------------------
|
*/

// 기본 홈
Route::get('/', ['as' => 'home', 'uses' => 'MainController@index'] );

// 로그인 후 리다이렉트
Route::get('/home', function() {
    return redirect(route('home'));
});

// 게시판 그룹별 메인
Route::get('/groups/{group}', ['as' => 'group', 'uses' => 'GroupsController@index'] );

// 전체 검색 결과
Route::get('/searches', ['as' => 'search', 'uses' => 'SearchController@result'] );

// 회원 가입
Route::get('users/join', ['as' => 'user.join', 'uses' => 'Auth\RegisterController@join']);
Route::get('users/register_form', ['as' => 'user.register.form.get', 'uses' => 'Auth\RegisterController@registerFormGet']);
Route::post('users/register_form', ['as' => 'user.register.form', 'uses' => 'Auth\RegisterController@registerForm']);
Route::post('users/register', ['as' => 'user.register', 'uses' => 'Auth\RegisterController@register']);
Route::get('users/welcome', ['as' => 'user.welcome', 'uses' => 'UsersController@welcome']);

// 로그인
Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => '', 'uses' => 'Auth\LoginController@login']);
// 로그아웃
Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

// 비밀번호 재설정
Route::get('auth/remind', ['as' => 'remind.create', 'uses' => 'Auth\PasswordsController@getRemind']);
Route::post('auth/remind', ['as' => 'remind.store', 'uses' => 'Auth\PasswordsController@postRemind']);
Route::get('auth/reset/{token}', ['as' => 'reset.create', 'uses' => 'Auth\PasswordsController@getReset'])
    ->where('token', '[\pL-\pN]{64}');
Route::post('auth/reset', ['as' => 'reset.store', 'uses' => 'Auth\PasswordsController@postReset']);

// 소셜 로그인 - 콜백 함수에서 회원 로그인 여부로 분기 (콜백함수 경로 지정은 config/services.php 에서)
Route::get('social/{provider}', ['as' => 'social', 'uses' => 'Auth\SocialController@redirectToProvider']);
Route::get('social/{provider}/callback/', ['as' => 'social.callback', 'uses' => 'Auth\SocialController@handleProviderCallback']);
// 소셜 로그인 후 회원가입
Route::post('social/socialUserJoin', ['as' => 'social.socialUserJoin', 'uses' => 'Auth\SocialController@socialUserJoin']);
// 소셜 로그인 후 기존 계정에 연결
Route::post('social/connectExistAccount', ['as' => 'social.connectExistAccount', 'uses' => 'Auth\SocialController@connectExistAccount']);

Route::group(['middleware' => 'valid.user'], function () {
    // 자기소개
    Route::get('users/profile/{toUser}', ['as' => 'user.profile', 'uses' => 'UsersController@profile']);

    // 툴팁 : 메일 보내기
    Route::get('users/mails/send/{toUser}', ['as' => 'user.mail.form', 'uses' => 'UsersController@form'])->middleware('form.mail');
    Route::post('users/mails/send', ['as' => 'user.mail.send', 'uses' => 'UsersController@send'])->middleware('send.mail');
});
// 메일 인증 이메일 주소 변경
Route::get('users/emails/edit/{email}', ['as' => 'user.email.edit', 'uses' => 'UsersController@editEmail']);
Route::put('users/emails/update', ['as' => 'user.email.update', 'uses' => 'UsersController@updateEmail']);

// 이메일 인증 라우트
Route::get('users/certify/id/{id}/crypt/{crypt}', ['as' => 'user.email.certify', 'uses' => 'UsersController@emailCertify']);

// 닉네임, 이메일 사용이 가능한지 검사
Route::post('register/validate', ['as' => 'register.validate', 'uses' => 'Auth\RegisterController@registerValidate']);

// 처리 결과 메세지를 alert창으로 알려주는 페이지
Route::get('messages', ['as' => 'message', 'uses' => 'MessagesController@message']);
// 처리 결과 메세지를 confirm창으로 알려주는 페이지
Route::get('confirms', ['as' => 'confirm', 'uses' => 'MessagesController@confirm']);

Route::group(['middleware' => ['web'], 'prefix' => 'bbs/{boardName}'], function()
{
    // 글 목록 + 검색
    Route::get('', ['as' => 'board.index', 'uses' => 'WritesController@index'])
        ->middleware(['level.board:list_level', 'valid.board'])
        ->where('boardName', '[a-zA-Z0-9_]+');
    // 글 읽기
    Route::get('views/{writeId}', ['as' => 'board.view', 'uses' => 'WritesController@view'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write', 'comment.view.parent', 'secret.board');
    // 글 읽기 중 링크 연결
    Route::get('views/{writeId}/link/{linkNo}', ['as' => 'board.link', 'uses' => 'WritesController@link'])
        ->middleware('level.board:link_level', 'valid.board', 'valid.write');
    // 글 읽기 중 파일 다운로드
    Route::get('views/{writeId}/download/{fileNo}', ['as' => 'board.download', 'uses' => 'BoardFilesController@download'])
        ->middleware('level.board:download_level', 'valid.board', 'valid.write');
    // 글 읽기 중 추천/비추천
    Route::post('views/{writeId}/{good}', ['as' => 'board.good', 'uses' => 'BoardGoodController@good'])
        ->where('good', 'good|nogood')
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 쓰기
    Route::get('create', ['as' => 'board.create', 'uses' => 'WritesController@create'])
        ->middleware('level.board:write_level', 'valid.board');
    Route::post('', ['as' => 'board.store', 'uses' => 'WritesController@store'])
        ->middleware('level.board:write_level', 'valid.board', 'valid.store.write');
    // 글 수정
    Route::get('edit/{writeId}', ['as' => 'board.edit', 'uses' => 'WritesController@edit'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write', 'can.action.write.immediately:edit', 'updatable.deletable.write');
    Route::put('update/{writeId}', ['as' => 'board.update', 'uses' => 'WritesController@update'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write', 'valid.store.write');
    // 글 삭제
    Route::get('delete/{writeId}', ['as' => 'board.destroy', 'uses' => 'WritesController@destroy'])
        ->middleware('valid.board', 'valid.write', 'can.action.write.immediately:delete', 'updatable.deletable.write');
    // 답변 쓰기
    Route::get('reply/{writeId}', ['as' => 'board.create.reply', 'uses' => 'WritesController@createReply'])
        ->middleware('level.board:reply_level', 'valid.board', 'valid.write');

    // 댓글 쓰기
    Route::post('comments/store', ['as' => 'board.comment.store', 'uses' => 'CommentsController@store'])
        ->middleware('level.board:comment_level');
    // 댓글 수정
    Route::put('comments/update', ['as' => 'board.comment.update', 'uses' => 'CommentsController@update'])
        ->middleware('level.board:comment_level', 'updatable.deletable.write');
    // 댓글 삭제
    Route::get('views/{writeId}/delete/{commentId}', ['as' => 'board.comment.destroy', 'uses' => 'CommentsController@destroy'])
        ->middleware('level.board:comment_level', 'can.delete.comment.immediately', 'updatable.deletable.write');

    // 커뮤니티에서의 관리자 기능
    // 글 목록 : 선택 삭제, 선택 복사, 선택 이동,
    // 글 보기 : 복사, 이동, 삭제, 수정
    Route::group(['middleware' => ['auth', 'admin.board', 'valid.board']], function() {
        // 복사, 이동 폼
        Route::get('move', ['as' => 'board.view.move', 'uses' => 'BoardMoveController@move']);
        // 선택 복사, 이동 폼
        Route::post('move', ['as' => 'board.list.move', 'uses' => 'BoardMoveController@move']);
        // 이동, 복사 수행
        Route::post('move/update', ['as' => 'board.update.move', 'uses' => 'BoardMoveController@moveUpdate']);
        // 선택 삭제
        Route::delete('delete/ids/{writeId}', ['as' => 'board.delete.ids', 'uses' => 'WritesController@selectedDelete'])
            ->middleware('valid.write');
    });

    // RSS
    Route::get('rss', ['as' => 'rss', 'uses' => 'WritesController@rss']);
});

Route::group(['middleware' => 'web', 'prefix' => 'password'], function()
{
    // 비밀 글, 댓글 읽기 전, 댓글삭제 전 비밀번호 검사
    Route::get('type/{type}', ['as' => 'board.password.check', 'uses' => 'PasswordController@checkPassword']);
    Route::post('compare', ['as' => 'board.password.compare', 'uses' => 'PasswordController@comparePassword']);
});

Route::group(['middleware' => 'web'], function()
{
    // 이미지 관련
    Route::group(['prefix' => 'images'], function () {
        // 원본 이미지 보기
        Route::get('original/{boardId?}', ['as' => 'image.original', 'uses' => 'BoardFilesController@viewOriginal']);
        // 에디터에서 이미지 업로드 팝업 페이지
        Route::get('upload', ['as' => 'image.form', 'uses' => 'BoardFilesController@popup']);
        // 에디터에서 이미지 업로드 실행
        Route::post('upload', ['as' => 'image.upload', 'uses' => 'BoardFilesController@uploadImage']);
    });

    // filter
    Route::post('ajax/filter/board', ['as' => 'ajax.filter.board', 'uses' => 'WritesController@boardFilter']);
    Route::post('ajax/filter/user', ['as' => 'ajax.filter.user', 'uses' => 'UsersController@userFilter']);
});

Route::group(['middleware' => ['web', 'auth'] ], function() {
    // 회원 정보 수정 폼으로 이동
    Route::get('users/edit', ['as' => 'user.edit', 'uses' => 'UsersController@edit']);
    // 회원정보 수정 수행
    Route::put('users/update', ['as' => 'user.update', 'uses' => 'UsersController@update']);
    // 비밀번호 검사 폼으로 이동
    Route::get('users/check_password', ['as' => 'user.checkPassword', 'uses' => 'UsersController@checkPassword']);
    // 최초 비밀번호 설정
    Route::post('users/set_password', ['as' => 'user.setPassword', 'uses' => 'UsersController@setPassword']);
    // 비밀번호 검사 수행
    Route::post('users/confirm_password', ['as' => 'user.confirmPassword', 'uses' => 'UsersController@confirmPassword']);
    // 회원 탈퇴
    Route::get('users/leave', ['as' => 'user.leave', 'uses' => 'UsersController@leave']);
    // 회원 포인트 내역
    Route::get('users/point/{id}', ['as' => 'user.point', 'uses' => 'PointsController@history']);
    // 회원 정보 수정 - 소셜 로그인 계정 연결 해제
    Route::post('users/disconnectSocialAccount', ['as' => 'user.disconnectSocialAccount', 'uses' => 'UsersController@disconnectSocialAccount']);

    // 스크랩
    Route::post('scraps', ['as' => 'scrap.store', 'uses' => 'ScrapsController@store'])
        ->middleware('level.board:comment_level');
    Route::resource('scraps', 'ScrapsController', [
            'only' => [
                'index', 'create', 'store', 'destroy',
            ],
            'names' => [
                'index' => 'scrap.index',
                'create' => 'scrap.create',
                'store' => 'scrap.store',
                'destroy' => 'scrap.destroy',
            ],
    ]);

    // 임시 저장
    Route::group(['middleware' => 'valid.user'], function () {
        Route::resource('autosave', 'AutosaveController', [
            'only' => [
                'index', 'show', 'store', 'destroy'
            ],
            'names' => [
                'index' => 'autosave.index',
                'show' => 'autosave.show',
                'store' => 'autosave.store',
                'destroy' => 'autosave.destroy'
            ]
        ]);
    });
});

// 쪽지
Route::group(['middleware' => ['web', 'auth', 'valid.user'] ], function()
{
    Route::get('memo/create/{toUser?}', ['as' => 'memo.create', 'uses' => 'MemosController@create']);
    Route::resource('memo', 'MemosController', [
        'except' => [
            'edit', 'update', 'create',
        ],
        'names' => [
            'index' => 'memo.index',
            'show' => 'memo.show',
            'store' => 'memo.store',
            'destroy' => 'memo.destroy',
        ],
    ]);
});


// 새글
Route::get('news', ['as' => 'new.index', 'uses' => 'BoardNewsController@index']);
Route::post('news', ['as' => 'new.destroy', 'uses' => 'BoardNewsController@destroy'])
    ->middleware('super');

/*
|--------------------------------------------------------------------------
| 관리자
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'] ], function() {
    // 관리자 메인
    Route::get('', ['as' => 'admin.index', 'uses' => 'Admin\MainController@index']);
    Route::get('index', ['as' => 'admin.index', 'uses' => 'Admin\MainController@index']);

    // 기본 환경 설정
    Route::get('configs', ['as' => 'admin.config', 'uses' => 'Admin\ConfigsController@index']);
    Route::put('configs/update', ['as' => 'admin.config.update', 'uses' => 'Admin\ConfigsController@update'])->middleware('super');

    // 관리 권한 설정 리소스 컨트롤러
    Route::resource('manage/auths', 'Admin\ManageAuthsController', [
        'only' => [
            'index', 'store', 'destroy',
        ],
        'names' => [
            'index' => 'admin.manageAuth.index',
            'store' => 'admin.manageAuth.store',
            'destroy' => 'admin.manageAuth.destroy',
        ]
    ]);

    // 테마 설정
    Route::get('themes', ['as' => 'admin.themes.index', 'uses' => 'Admin\ThemesController@index']);
    Route::post('themes/update', ['as' => 'admin.themes.update', 'uses' => 'Admin\ThemesController@update'])->middleware('super');
    Route::post('themes/update/skins', ['as' => 'admin.themes.update.skin', 'uses' => 'Admin\ThemesController@updateSkins'])->middleware('super');
    // 테마 상세보기 레이어
    Route::post('themes/detail', ['as' => 'admin.themes.detail', 'uses' => 'Admin\ThemesController@detail']);
    // 테마 미리보기
    Route::get('themes/previews/{themeName}/index', ['as' => 'admin.themes.preview.index', 'uses' => 'Admin\ThemePreviewsController@index']);
    Route::get('themes/previews/{themeName}/boards/lists', ['as' => 'admin.themes.preview.board.list', 'uses' => 'Admin\ThemePreviewsController@boardList']);
    Route::get('themes/previews/{themeName}/boards/view', ['as' => 'admin.themes.preview.board.view', 'uses' => 'Admin\ThemePreviewsController@boardView']);

    Route::get('menus', ['as' => 'admin.menus.index', 'uses' => 'Admin\MenusController@index']);
    Route::get('menus/create', ['as' => 'admin.menus.create', 'uses' => 'Admin\MenusController@create']);
    Route::post('menus', ['as' => 'admin.menus.store', 'uses' => 'Admin\MenusController@store'])->middleware('super');
    // 메뉴 추가 팝업창에 대상 선택에 따라서 view를 load하는 기능
    Route::post('menus/result', ['as' => 'admin.menus.result', 'uses' => 'Admin\MenusController@result']);

    // 메일 발송 테스트
    Route::get('mails', ['as' => 'admin.email', 'uses' => 'Admin\MailsController@index']);
    Route::post('mails/send', ['as' => 'admin.email.send', 'uses' => 'Admin\MailsController@postMail']);

    // 세션 일괄 삭제
    Route::get('sessions/delete', ['as' => 'admin.session.delete', 'uses' => 'Admin\ExtrasController@deleteSession']);
    // 캐시 일괄 삭제
    Route::get('caches/delete', ['as' => 'admin.cache.delete', 'uses' => 'Admin\ExtrasController@deleteCache']);
    // 썸네일 일괄 삭제
    Route::get('thumbnails/delete', ['as' => 'admin.thumbnail.delete', 'uses' => 'Admin\ExtrasController@deleteThumbnail']);

    // phpinfo()
    Route::get('phpinfo', ['as' => 'admin.phpinfo', 'uses' => 'Admin\ExtrasController@phpinfo']);

    // 부가서비스
    Route::get('extra_service', ['as' => 'admin.extra_service', 'uses' => 'Admin\ExtrasController@extraService']);

    // 회원관리 리소스 컨트롤러에 추가적으로 라우팅을 구성(리소스 라우트보다 앞에 와야 함)
    Route::put('users/selected_update', ['as' => 'admin.users.selectedUpdate', 'uses' => 'Admin\UsersController@selectedUpdate']);
    // 회원관리 리소스 컨트롤러
    Route::resource('users', 'Admin\UsersController', [
        'except' => [
            'show',
        ],
        'names' => [
            'create' => 'admin.users.create',
            'index' => 'admin.users.index',
            'store' => 'admin.users.store',
            'destroy' => 'admin.users.destroy',
            'update' => 'admin.users.update',
            'edit' => 'admin.users.edit',
        ]
    ]);

    // 포인트 관리 리소스 컨트롤러
    Route::resource('points', 'Admin\PointsController', [
        'only' => [
            'index', 'store', 'destroy',
        ],
        'names' => [
            'index' => 'admin.points.index',
            'store' => 'admin.points.store',
            'destroy' => 'admin.points.destroy',
        ]
    ]);

    // 게시판 관리 리소스 컨트롤러에 추가적으로 라우팅을 구성(리소스 라우트보다 앞에 와야 함)
    Route::get('boards/{boardName}/copy', ['as' => 'admin.boards.copyForm', 'uses' => 'Admin\BoardsController@copyForm']);
    Route::post('boards/copy', ['as' => 'admin.boards.copy', 'uses' => 'Admin\BoardsController@copy']);
    Route::get('boards/{boardName}/thumbnail/delete', ['as' => 'admin.boards.thumbnail.delete', 'uses' => 'Admin\BoardsController@deleteThumbnail']);
    Route::put('boards/selected_update', ['as' => 'admin.boards.selectedUpdate', 'uses' => 'Admin\BoardsController@selectedUpdate']);
    // 게시물 순서 조정
    Route::get('boards/{boardName}/order', ['as' => 'admin.boards.orderList', 'uses' => 'Admin\BoardsController@orderList']);
    Route::put('boards/order', ['as' => 'admin.boards.adjustOrder', 'uses' => 'Admin\BoardsController@adjustOrder']);
    // 게시판 관리 리소스 컨트롤러
    Route::resource('boards', 'Admin\BoardsController', [
        'except' => [
            'show',
        ],
        'names' => [
            'create' => 'admin.boards.create',
            'index' => 'admin.boards.index',
            'store' => 'admin.boards.store',
            'destroy' => 'admin.boards.destroy',
            'update' => 'admin.boards.update',
            'edit' => 'admin.boards.edit',
        ]
    ]);

    // 게시판 그룹 관리 리소스 컨트롤러에 추가적으로 라우팅을 구성(리소스 라우트보다 앞에 와야 함)
    Route::put('groups/selected_update', ['as' => 'admin.groups.selectedUpdate', 'uses' => 'Admin\GroupsController@selectedUpdate']);
    // 게시판 그룹관리 리소스 컨트롤러
    Route::resource('groups', 'Admin\GroupsController', [
        'except' => [
            'show',
        ],
        'names' => [
            'create' => 'admin.groups.create',
            'index' => 'admin.groups.index',
            'store' => 'admin.groups.store',
            'destroy' => 'admin.groups.destroy',
            'update' => 'admin.groups.update',
            'edit' => 'admin.groups.edit',
        ]
    ]);

    // (회원) 접근가능그룹 리소스 컨트롤러
    Route::resource('accessgroups', 'Admin\AccessGroupsController', [
        'only' => [
            'show', 'store', 'destroy',
        ],
        'names' => [
            'show' => 'admin.accessGroups.show',
            'store' => 'admin.accessGroups.store',
            'destroy' => 'admin.accessGroups.destroy',
        ],
    ]);

    // (그룹) 접근가능회원 리소스 컨트롤러
    Route::resource('accessusers', 'Admin\AccessUsersController', [
        'only' => [
            'show', 'destroy',
        ],
        'names' => [
            'show' => 'admin.accessUsers.show',
            'destroy' => 'admin.accessUsers.destroy',
        ],
    ]);

    // 글,댓글 현황
    Route::get('status', ['as' => 'admin.status', 'uses' => 'Admin\StatusController@index']);

    // 모듈 설정
    Route::get('modules/manage', ['as' => 'admin.modules.manage', 'uses' => 'Admin\ModulesController@module']);
    Route::post('modules/active', ['as' => 'admin.modules.active', 'uses' => 'Admin\ModulesController@active']);
    Route::post('modules/inactive', ['as' => 'admin.modules.inactive', 'uses' => 'Admin\ModulesController@inactive']);
    Route::delete('modules', ['as' => 'admin.modules.destroy', 'uses' => 'Admin\ModulesController@destroy']);
    Route::resource('modules', 'Admin\ModulesController', [
        'except' => [
            'destroy',
        ],
        'names' => [
            'index' => 'admin.modules.index',
            'create' => 'admin.modules.create',
            'store' => 'admin.modules.store',
            'show' => 'admin.modules.show',
            'edit' => 'admin.modules.edit',
            'update' => 'admin.modules.update',
            'destroy' => 'admin.modules.destroy',
        ],
    ]);

});
