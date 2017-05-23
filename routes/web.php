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
Route::get('/index', ['as' => 'index', 'uses' => 'WelcomeController@index']);
Route::get('/', 'ThemeController@index');
Route::get('/menuTest', ['as' => 'menuTest', 'uses' => 'ThemeController@menuTest']);

// 로그인 후 리다이렉트 되는 페이지
Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index'] );

// 관리자 그룹
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    // 관리자 검색 기능
    Route::get('search', ['as' => 'admin.search', 'uses' => 'Admin\SearchController@search']);
    // 관리자 메인
    Route::get('index', ['as' => 'admin.index', 'uses' => 'Admin\IndexController@index']);
    // 회원관리 리소스 컨트롤러에 추가적으로 라우팅을 구성(리소스 라우트보다 앞에 와야 함)
    Route::put('users/selected_update', ['as' => 'admin.users.selectedUpdate', 'uses' => 'Admin\UsersController@selectedUpdate']);
    // 환경 설정
    Route::get('config', ['as' => 'admin.config', 'uses' => 'Admin\ConfigController@index']);
    Route::put('config/update/{name}', ['as' => 'admin.config.update', 'uses' => 'Admin\ConfigController@update']);
    // 회원관리 CRUD 컨트롤러
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
    // 회원 관리 -> 접근가능그룹 리소스 컨트롤러
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
    // 게시판 그룹 관리 -> 그룹 접근가능회원 리소스 컨트롤러
    Route::resource('accessible_users', 'Admin\AccessibleUsersController', [
        'only' => [
            'show', 'destroy',
        ],
        'names' => [
            'show' => 'admin.accessUsers.show',
            'destroy' => 'admin.accessUsers.destroy',
        ],
    ]);

    // 게시판 관리 리소스 컨트롤러에 추가적으로 라우팅을 구성(리소스 라우트보다 앞에 와야 함)
    Route::put('boards/selected_update', ['as' => 'admin.boards.selectedUpdate', 'uses' => 'Admin\BoardsController@selectedUpdate']);
    Route::get('boards/copy/{boardId}', ['as' => 'admin.boards.copyForm', 'uses' => 'Admin\BoardsController@copyForm']);
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
});

// 커뮤니티, 인증이 필요한 라우트 그룹
Route::group(['middleware' => 'auth'], function() {
    // 사용자가 회원 정보 수정할 때 관련한 라우트들
    Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'User\UserController@edit']);
    Route::put('user/update', ['as' => 'user.update', 'uses' => 'User\UserController@update']);
    Route::get('user/check_password', ['as' => 'user.checkPassword', 'uses' => 'User\UserController@checkPassword']);
    Route::post('user/set_password', ['as' => 'user.setPassword', 'uses' => 'User\UserController@setPassword']);
    Route::post('user/confirm_password', ['as' => 'user.confirmPassword', 'uses' => 'User\UserController@confirmPassword']);
    Route::get('user/point/{id}', ['as' => 'user.point', 'uses' => 'User\PointController@index']);
    // 회원 정보 수정 - 소셜 로그인 계정 연결 해제
    Route::post('user/disconnectSocialAccount', ['as' => 'user.disconnectSocialAccount', 'uses' => 'User\UserController@disconnectSocialAccount']);
});

// 인증에 관련한 라우트들
Auth::routes();

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

// 이메일 인증 라우트
Route::get('emailCertify/id/{id}/crypt/{crypt}', 'User\MailController@emailCertify')->name('emailCertify');
// 처리 결과 메세지를 경고창으로 알려주는 페이지
Route::get('message', ['as' => 'message', 'uses' => 'Message\MessageController@message']);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 게시판
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


Route::group(['prefix' => 'board/{boardId}'], function () {
    // 글 목록 + 검색
    Route::get('', ['as' => 'board.index', 'uses' => 'Board\BoardController@index'])
        ->middleware(['level.board:list_level', 'valid.board'])
        ->where('boardId', '[0-9]+');
    // 글 읽기
    Route::get('view/{writeId}', ['as' => 'board.view', 'uses' => 'Board\BoardController@view'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write', 'secret.board');
    // 비밀 글 읽기 전 비밀번호 검사
    Route::get('view/{writeId}/password', ['as' => 'board.password', 'uses' => 'Board\BoardController@checkPassword'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    Route::post('validatePassword', ['as' => 'board.validatePassword', 'uses' => 'Board\BoardController@validatePassword'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 읽기 중 링크 연결
    Route::get('view/{writeId}/link/{linkNo}', ['as' => 'board.link', 'uses' => 'Board\BoardController@link'])
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 읽기 중 파일 다운로드
    Route::get('view/{writeId}/download/{fileNo}', ['as' => 'board.download', 'uses' => 'Board\BoardController@download'])
        ->middleware('level.board:download_level', 'valid.board', 'valid.write');
    // 글 읽기 중 추천/비추천
    Route::post('view/{writeId}/{good}', ['as' => 'board.good', 'uses' => 'Board\BoardController@good'])
        ->where('good', 'good|nogood')
        ->middleware('level.board:read_level', 'valid.board', 'valid.write');
    // 글 쓰기
    Route::get('create', ['as' => 'board.create', 'uses' => 'Board\BoardController@create'])
        ->middleware('level.board:write_level', 'valid.board');
    Route::post('', ['as' => 'board.store', 'uses' => 'Board\BoardController@store'])
        ->middleware('level.board:write_level', 'valid.board', 'store.write');

    // 글 수정
    Route::get('edit/{writeId}', ['as' => 'board.edit', 'uses' => 'Board\BoardController@edit'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write');
    Route::put('update/{writeId}', ['as' => 'board.update', 'uses' => 'Board\BoardController@update'])
        ->middleware('level.board:update_level', 'valid.board', 'valid.write', 'store.write');
    // 글 삭제
    Route::get('delete/{writeId}', ['as' => 'board.destroy', 'uses' => 'Board\BoardController@destroy'])
        ->middleware('valid.write');
    // 커뮤니티에서의 관리자 기능
    // 글 목록 : 선택 삭제, 선택 복사, 선택 이동,
    // 글 보기 : 복사, 이동, 삭제, 수정
    Route::group(['middleware' => ['auth', 'level:10', 'valid.board']], function() {
        // 복사, 이동 폼
        Route::get('move', ['as' => 'board.view.move', 'uses' => 'Board\BoardController@move']);
        // 선택 복사, 이동 폼
        Route::post('move', ['as' => 'board.list.move', 'uses' => 'Board\BoardController@move']);
        // 이동, 복사 수행
        Route::post('move/update', ['as' => 'board.moveUpdate', 'uses' => 'Board\BoardController@moveUpdate']);
        // 선택 삭제
        Route::delete('delete/ids/{writeId}', ['as' => 'board.delete.ids', 'uses' => 'Board\BoardController@selectedDelete'])
            ->middleware('valid.write');
    });
});

// 이미지 관련 라우트
Route::group(['prefix' => 'image'], function () {
    // 원본 이미지 보기
    Route::get('original/{boardId?}', ['as' => 'image.original', 'uses' => 'Board\ImageController@viewImage']);
    // 에디터에서 이미지 업로드 팝업 페이지
    Route::get('upload', ['as' => 'image.form', 'uses' => 'Board\ImageController@showImagePop']);
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
