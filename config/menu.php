<?php
return [
    '100000' => ['환경설정', '', 0],
    '100100' => ['기본환경설정', 'admin.config', 1],
    '100200' => ['관리권한설정', 'admin.manageAuth.index', 1],
    '100300' => ['테마설정', 'admin.themes.index', 1],
    '100400' => ['메뉴설정', 'admin.menus.index', 1],
    '100500' => ['메일테스트', 'admin.email', 0],
    '100600' => ['팝업레이어관리', 'admin.popups.index', 0],
    '100700' => ['세션파일 일괄삭제', 'admin.session.delete', 1],
    '100710' => ['캐시파일 일괄삭제', 'admin.cache.delete', 1],
    '100720' => ['썸네일파일 일괄삭제', 'admin.thumbnail.delete', 1],
    '100800' => ['phpinfo()', 'admin.phpinfo', 0],
    '100810' => ['부가서비스', 'admin.extra_service', 0],

    '200000' => ['회원관리', '', 0],
    '200100' => ['회원관리', 'admin.users.index', 0],
    '200200' => ['포인트관리', 'admin.points.index', 0],

    '300000' => ['게시판관리', '', 0],
    '300100' => ['게시판관리', 'admin.boards.index', 0],
    '300200' => ['게시판그룹관리', 'admin.groups.index', 0],
    '300300' => ['인기검색어관리', 'admin.populars.index', 0],
    '300310' => ['인기검색어순위', 'admin.populars.rank', 0],
    // '300400' => ['내용관리', 'admin.contents.index', 0],
    '300500' => ['글,댓글 현황', 'admin.status', 0],

];
