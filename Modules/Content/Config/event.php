<?php

return [
    // 레이아웃 하단에 내용관리 목록을 추가한다.
    'footerContents' => [
        'addFooterContent' => [
            'module' => 'Content',
            'priority' => 1,
            'use' => 1,
            'description' => '레이아웃 하단에 내용보기 목록 추가',
        ],
    ],
    // 메뉴설정 > 메뉴 추가의 대상 선택에 내용관리를 추가한다.
    'menuSelectOption' => [
        'addMenuSelectOption' => [
            'module' => 'Content',
            'priority' => 1,
            'use' => 1,
            'description' => '메뉴설정 > 메뉴 추가 > 대상 선택 - 내용관리 추가',
        ],
    ],
    // 메뉴설정 > 메뉴 추가 > 내용관리를 선택했을 때 나오는 내용관리 리스트를 추가한다.
    'menuResult' => [
        'addMenuResult' => [
            'module' => 'Content',
            'priority' => 1,
            'use' => 1,
            'description' => '메뉴설정 > 메뉴 추가 > 내용관리 - 내용관리 리스트 추가',
        ],
    ],
];
