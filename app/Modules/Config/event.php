<?php

return [
    'headerLefts' => [
        'addLogo' => [
            'module' => 'LaonBoard',
            'priority' => 1,
            'use' => 1,
            'description' => '레이아웃 - 로고 추가',
        ],
    ],
    'headerContents' => [
        'addSearchBar' => [
            'module' => 'LaonBoard',
            'priority' => 5,
            'use' => 1,
            'description' => '레이아웃 - 전체검색 바 추가',
        ],
        'addMenuBar' => [
            'module' => 'LaonBoard',
            'priority' => 1,
            'use' => 1,
            'description' => '레이아웃 - 메뉴 바 추가',
        ],
    ],
    'mainContents' => [
        'addLatestList' => [
            'module' => 'LaonBoard',
            'priority' => 5,
            'use' => 1,
            'description' => '메인 - 최신글 목록 추가',
        ],
    ],
    'footerContents' => [
        'addCopyright' => [
            'module' => 'LaonBoard',
            'priority' => 10,
            'use' => 1,
            'description' => '레이아웃 - Copyright 추가',
        ],
    ],
    'editUserInfo' => [
        'addToEditUserInfo' => [
            'module' => 'LaonBoard',
            'priority' => 1,
            'use' => 1,
            'description' => '회원정보수정 - 이름과 휴대폰 입력 기본 양식',
        ],
    ],
];
