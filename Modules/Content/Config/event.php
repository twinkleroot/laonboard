<?php

return [
    // 관리자 메뉴에 내용관리를 추가한다.
    'adminSubMenu' => [
        'Modules\Content\Events\AddAdminMenu' => ['priority' => 1],
    ],
    // 레이아웃 하단에 내용관리 목록을 추가한다.
    'footerContent' => [
        'Modules\Content\Events\AddFooterContent' => ['priority' => 1],
    ],
    // 메뉴설정 > 메뉴 추가의 대상 선택에 내용관리를 추가한다.
    'menuSelectOption' => [
        'Modules\Content\Events\AddMenuSelectOption' => ['priority' => 1],
    ],
    // 위에서 내용관리를 선택했을 때 나오는 내용관리 리스트를 추가한다.
    'menuResult' => [
        'Modules\Content\Events\AddMenuResult' => ['priority' => 1],
    ],
];
