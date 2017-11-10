<?php

return [
    'menuContents' => [
        'addNotificationMenu' => [
            'module' => 'Inform',
            'priority' => 1,
            'use' => 1,
            'description' => '레이아웃 - 사용자 메뉴에 알림 추가',
        ],
    ],
    'afterStoreWrite' => [
        'sendInformAboutWrite' => [
            'module' => 'Inform',
            'priority' => 1,
            'use' => 1,
            'description' => '글쓰기 후 알림 전송',
        ],
    ],
    'afterStoreComment' => [
        'sendInformAboutComment' => [
            'module' => 'Inform',
            'priority' => 1,
            'use' => 1,
            'description' => '댓글쓰기 후 알림 전송',
        ],
    ],
    'afterLogin' => [
        'deleteExpireInforms' => [
            'module' => 'Inform',
            'priority' => 1,
            'use' => 1,
            'description' => '유효기간이 만료된 알림 삭제',
        ],
    ],
];
