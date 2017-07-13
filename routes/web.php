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

// 기본 홈
Route::get('/', ['as' => 'home', 'uses' => 'MainController@index'] );
// 로그인 후 리다이렉트
Route::get('/home', ['as' => 'home', 'uses' => 'MainController@index'] );
// 게시판 그룹별 메인
Route::get('/group/{group}', ['as' => 'group', 'uses' => 'MainController@groupIndex'] );

// 전체 검색 결과
Route::get('/search', ['as' => 'search', 'uses' => 'Search\SearchController@result'] );

// 관리자 그룹
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin.menu'] ], function() {
    // 관리자 메인
    Route::get('index', ['as' => 'admin.index', 'uses' => 'Admin\IndexController@index']);

    // 기본 환경 설정
    Route::get('config', ['as' => 'admin.config', 'uses' => 'Admin\ConfigController@index']);
    Route::put('config/update/{name}', ['as' => 'admin.config.update', 'uses' => 'Admin\ConfigController@update']);

    // 관리 권한 설정 리소스 컨트롤러
    Route::resource('manageAuth', 'Admin\ManageAuthController', [
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
    Route::get('theme', ['as' => 'admin.themes.index', 'uses' => 'Admin\ThemeController@index']);

    // 메뉴 추가 팝업창에 대상 선택에 따라서 view를 load하는 기능
    Route::post('menus/result', ['as' => 'admin.menus.result', 'uses' => 'Admin\MenusController@result']);
    // 메뉴 설정 리소스 컨트롤러
    Route::resource('menus', 'Admin\MenusController', [
        'only' => [
            'index', 'create', 'store',
        ],
        'names' => [
            'index' => 'admin.menus.index',
            'create' => 'admin.menus.create',
            'store' => 'admin.menus.store',
        ]
    ]);

    // 메일 발송 테스트
    Route::get('mail', ['as' => 'admin.email', 'uses' => 'Admin\MailController@index']);
    Route::post('mail/send', ['as' => 'admin.email.send', 'uses' => 'Admin\MailController@postMail']);

    // 팝업레이어 관리 리소스 컨트롤러
    Route::get('popups/{id}', ['as' => 'admin.popups.destroy', 'uses' => 'Admin\PopupsController@destroy'])
        ->where('id', '[0-9]+');
    Route::resource('popups', 'Admin\PopupsController', [
        'except' => [
            'show', 'destroy',
        ],
        'names' => [
            'index' => 'admin.popups.index',
            'create' => 'admin.popups.create',
            'store' => 'admin.popups.store',
            'edit' => 'admin.popups.edit',
            'update' => 'admin.popups.update',
        ]
    ]);

    // 세션 일괄 삭제
    Route::get('session/delete', ['as' => 'admin.session.delete', 'uses' => 'Admin\SimpleController@deleteSession']);
    // 캐시 일괄 삭제
    Route::get('cache/delete', ['as' => 'admin.cache.delete', 'uses' => 'Admin\SimpleController@deleteCache']);
    // 썸네일 일괄 삭제
    Route::get('thumbnail/delete', ['as' => 'admin.thumbnail.delete', 'uses' => 'Admin\SimpleController@deleteThumbnail']);

    // phpinfo()
    Route::get('phpinfo', ['as' => 'admin.phpinfo', 'uses' => 'Admin\SimpleController@phpinfo']);

    // 부가서비스
    Route::get('extra_service', ['as' => 'admin.extra_service', 'uses' => 'Admin\SimpleController@extraService']);

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
    Route::put('boards/selected_update', ['as' => 'admin.boards.selectedUpdate', 'uses' => 'Admin\BoardsController@selectedUpdate']);
    Route::get('boards/copy/{boardId}', ['as' => 'admin.boards.copyForm', 'uses' => 'Admin\BoardsController@copyForm']);
    Route::get('boards/{boardId}/thumbnail/delete', ['as' => 'admin.boards.thumbnail.delete', 'uses' => 'Admin\BoardsController@deleteThumbnail']);
    Route::post('boards/copy', ['as' => 'admin.boards.copy', 'uses' => 'Admin\BoardsController@copy']);
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
    Route::resource('accessible_groups', 'Admin\AccessibleGroupsController', [
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
    Route::resource('accessible_users', 'Admin\AccessibleUsersController', [
        'only' => [
            'show', 'destroy',
        ],
        'names' => [
            'show' => 'admin.accessUsers.show',
            'destroy' => 'admin.accessUsers.destroy',
        ],
    ]);

    // 인기 검색어 관리
    Route::get('populars/index', ['as' => 'admin.populars.index', 'uses' => 'Admin\PopularsController@index']);
    Route::delete('populars/destroy/{ids}', ['as' => 'admin.populars.destroy', 'uses' => 'Admin\PopularsController@destroy']);
    // 인기 검색어 순위
    Route::get('populars/rank', ['as' => 'admin.populars.rank', 'uses' => 'Admin\PopularsController@rank']);

    // 내용 관리
    Route::get('contents/{content}/delete', ['as' => 'admin.contents.destroy', 'uses' => 'Admin\ContentsController@destroy']);
    Route::resource('contents', 'Admin\ContentsController', [
        'except' => [
            'destroy', 'show'
        ],
        'names' => [
            'index' => 'admin.contents.index',
            'create' => 'admin.contents.create',
            'store' => 'admin.contents.store',
            'edit' => 'admin.contents.edit',
            'update' => 'admin.contents.update',
        ],
    ]);

    // 글,댓글 현황
    Route::get('status', ['as' => 'admin.status', 'uses' => 'Admin\StatusController@index']);


});


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 커뮤니티
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// 인증에 관련한 라우트들
Auth::routes();
// 인증이 필요한 라우트 그룹
Route::group(['middleware' => 'auth'], function() {
    // 사용자가 회원 정보 수정할 때 관련한 라우트들
    Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'User\UserController@edit']);
    Route::put('user/update', ['as' => 'user.update', 'uses' => 'User\UserController@update']);
    Route::get('user/check_password', ['as' => 'user.checkPassword', 'uses' => 'User\UserController@checkPassword']);
    Route::post('user/set_password', ['as' => 'user.setPassword', 'uses' => 'User\UserController@setPassword']);
    Route::post('user/confirm_password', ['as' => 'user.confirmPassword', 'uses' => 'User\UserController@confirmPassword']);
    Route::get('user/leave', ['as' => 'user.leave', 'uses' => 'User\UserController@leave']);
    Route::get('user/point/{id}', ['as' => 'user.point', 'uses' => 'User\UserController@pointList']);
    // 회원 정보 수정 - 소셜 로그인 계정 연결 해제
    Route::post('user/disconnectSocialAccount', ['as' => 'user.disconnectSocialAccount', 'uses' => 'User\UserController@disconnectSocialAccount']);
    // 자기소개
    Route::get('user/profile/{id}', ['as' => 'user.profile', 'uses' => 'User\UserController@profile']);


    // 쪽지
    Route::get('memo/{memo}/delete', ['as' => 'memo.destroy', 'uses' => 'Memo\MemoController@destroy']);
    Route::resource('memo', 'Memo\MemoController', [
        'except' => [
            'edit', 'update', 'destroy'
        ],
        'names' => [
            'index' => 'memo.index',
            'show' => 'memo.show',
            'create' => 'memo.create',
            'store' => 'memo.store',
        ],
    ]);
});
// 내용관리 보기는 인증이 없어도 가능
Route::get('contents/{content}', ['as' => 'contents.show', 'uses' => 'Content\ContentsController@show']);

// 소셜 로그인 - 콜백 함수에서 회원 로그인 여부로 분기 (콜백함수 경로 지정은 config/services.php 에서)
Route::get('social/{provider}', ['as' => 'social', 'uses' => 'Auth\SocialController@redirectToProvider']);
Route::get('social/{provider}/callback/', ['as' => 'social.callback', 'uses' => 'Auth\SocialController@handleProviderCallback']);
// 소셜 로그인 후 회원가입
Route::post('social/socialUserJoin', ['as' => 'social.socialUserJoin', 'uses' => 'Auth\SocialController@socialUserJoin']);
// 소셜 로그인 후 기존 계정에 연결
Route::post('social/connectExistAccount', ['as' => 'social.connectExistAccount', 'uses' => 'Auth\SocialController@connectExistAccount']);

// 회원 가입
Route::get('user/join', ['as' => 'user.join', 'uses' => 'Auth\RegisterController@join']);
Route::post('user/register', ['as' => 'user.register', 'uses' => 'Auth\RegisterController@register']);
Route::get('user/welcome', ['as' => 'user.welcome', 'uses' => 'User\UserController@welcome']);
// 메일 인증 메일 주소 변경
Route::get('user/email/edit/{email}', ['as' => 'user.email.edit', 'uses' => 'User\UserController@editEmail']);
Route::put('user/email/update', ['as' => 'user.email.update', 'uses' => 'User\UserController@updateEmail']);

// 이메일 인증 라우트
Route::get('emailCertify/id/{id}/crypt/{crypt}', ['as' => 'emailCertify', 'uses' => 'User\MailController@emailCertify']);
// 처리 결과 메세지를 경고창으로 알려주는 페이지
Route::get('message', ['as' => 'message', 'uses' => 'Message\MessageController@message']);

Route::group(['prefix' => 'board/{boardId}'], function () {
    // 글 목록 + 검색
    Route::get('', ['as' => 'board.index', 'uses' => 'Board\WriteController@index'])
        ->middleware(['level.board:list_level', 'valid.board'])
        ->where('boardId', '[0-9]+');
    // 글 읽기
    Route::get('view/{writeId}', ['as' => 'board.view', 'uses' => 'Board\WriteController@view'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write', 'comment.view.parent', 'secret.board');
    // 글 읽기 중 링크 연결
    Route::get('view/{writeId}/link/{linkNo}', ['as' => 'board.link', 'uses' => 'Board\WriteController@link'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 읽기 중 파일 다운로드
    Route::get('view/{writeId}/download/{fileNo}', ['as' => 'board.download', 'uses' => 'Board\DownloadController@download'])
        ->middleware('level.board:download_level', 'valid.board', 'valid.write');
    // 글 읽기 중 추천/비추천
    Route::post('view/{writeId}/{good}', ['as' => 'board.good', 'uses' => 'Board\WriteController@good'])
        ->where('good', 'good|nogood')
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 쓰기
    Route::get('create', ['as' => 'board.create', 'uses' => 'Board\WriteController@create'])
        ->middleware('level.board:write_level', 'valid.board');
    Route::post('', ['as' => 'board.store', 'uses' => 'Board\WriteController@store'])
        ->middleware('level.board:write_level', 'valid.board', 'store.write', 'writable.reply');
    // 글 수정
    Route::get('edit/{writeId}', ['as' => 'board.edit', 'uses' => 'Board\WriteController@edit'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write', 'can.action.write.immediately:edit');
    Route::put('update/{writeId}', ['as' => 'board.update', 'uses' => 'Board\WriteController@update'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write', 'updatable.deletable.write', 'store.write');
    // 글 삭제
    Route::get('delete/{writeId}', ['as' => 'board.destroy', 'uses' => 'Board\WriteController@destroy'])
        ->middleware('valid.board', 'valid.write', 'can.action.write.immediately:delete', 'updatable.deletable.write');
    // 답변 쓰기
    Route::get('reply/{writeId}', ['as' => 'board.create.reply', 'uses' => 'Board\WriteController@createReply'])
        ->middleware('level.board:write_level', 'valid.board', 'valid.write', 'writable.reply');
    // 댓글 삽입
    Route::post('comment/store', ['as' => 'board.comment.store', 'uses' => 'Board\CommentController@store'])
        ->middleware('level.board:comment_level', 'writable.comment:create');
    // 댓글 수정
    Route::put('comment/update', ['as' => 'board.comment.update', 'uses' => 'Board\CommentController@update'])
        ->middleware('level.board:comment_level', 'writable.comment:update', 'updatable.deletable.write');
    // 댓글 삭제
    Route::get('view/{writeId}/delete/{commentId}', ['as' => 'board.comment.destroy', 'uses' => 'Board\CommentController@destroy'])
        ->middleware('level.board:comment_level', 'can.delete.comment.immediately', 'updatable.deletable.write');

    // 커뮤니티에서의 관리자 기능
    // 글 목록 : 선택 삭제, 선택 복사, 선택 이동,
    // 글 보기 : 복사, 이동, 삭제, 수정
    Route::group(['middleware' => ['auth', 'level:10', 'valid.board']], function() {
        // 복사, 이동 폼
        Route::get('move', ['as' => 'board.view.move', 'uses' => 'Board\MoveController@move']);
        // 선택 복사, 이동 폼
        Route::post('move', ['as' => 'board.list.move', 'uses' => 'Board\MoveController@move']);
        // 이동, 복사 수행
        Route::post('move/update', ['as' => 'board.moveUpdate', 'uses' => 'Board\MoveController@moveUpdate']);
        // 선택 삭제
        Route::delete('delete/ids/{writeId}', ['as' => 'board.delete.ids', 'uses' => 'Board\WriteController@selectedDelete'])
            ->middleware('valid.write');
    });

    // RSS
    Route::get('rss', ['as' => 'rss', 'uses' => 'Board\WriteController@rss'])
        ->middleware('rss');
});
// 비밀 글, 댓글 읽기 전, 댓글삭제 전 비밀번호 검사
Route::get('password/type/{type}', ['as' => 'board.password.check', 'uses' => 'Board\PasswordController@checkPassword']);
Route::post('password/compare', ['as' => 'board.password.compare', 'uses' => 'Board\PasswordController@comparePassword']);

// 스크랩
Route::get('scrap/{scrap}/delete', ['as' => 'scrap.destroy', 'uses' => 'Board\ScrapController@destroy'])
    ->middleware('auth');
Route::post('scrap', ['as' => 'scrap.store', 'uses' => 'Board\ScrapController@store'])
    ->middleware('level.board:comment_level', 'writable.comment:create');
Route::resource('scrap', 'Board\ScrapController', [
        'only' => [
            'index', 'create', 'store'
        ],
        'names' => [
            'index' => 'scrap.index',
            'create' => 'scrap.create',
            'store' => 'scrap.store',
        ],
        'middleware' => [
            'auth',
        ]
]);

// 새글
Route::get('new', ['as' => 'new.index', 'uses' => 'Board\NewController@index']);
Route::post('new', ['as' => 'new.destroy', 'uses' => 'Board\NewController@destroy'])
    ->middleware('auth');

// 이미지 관련
Route::group(['prefix' => 'image'], function () {
    // 원본 이미지 보기
    Route::get('original/{boardId?}', ['as' => 'image.original', 'uses' => 'Board\ImageController@viewOriginal']);
    // 에디터에서 이미지 업로드 팝업 페이지
    Route::get('upload', ['as' => 'image.form', 'uses' => 'Board\ImageController@popup']);
    // 에디터에서 이미지 업로드 실행
    Route::post('upload', ['as' => 'image.upload', 'uses' => 'Board\ImageController@uploadImage']);
});

// 임시 저장
Route::group(['middleware' => 'valid.user'], function () {
    Route::resource('autosave', 'Board\AutosaveController', [
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

// ajax api
Route::post('ajax/filter', ['as' => 'ajax.filter', 'uses' => 'Board\FilterController@filter']);
