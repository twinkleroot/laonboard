<?php

return [
    'footerUp' => [
        'addPopularSearchListToMain' => [
            'module' => 'PopularSearches',
            'priority' => 1,
            'use' => 1,
            'description' => '레이아웃 - 하단에 인기검색어 목록 표시',
        ],
    ],
    'afterSearch' => [
        'addPopularKeyword' => [
            'module' => 'PopularSearches',
            'priority' => 1,
            'use' => 1,
            'description' => '인기검색어 DB에 검색기록 추가',
        ],
    ],
];
