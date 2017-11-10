<?php

return [
    'registerForm' => [
        'addCertFuncToRegister' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '회원가입 양식 - 휴대폰 본인 확인 추가',
        ],
    ],
    'registerUserEnd' => [
        'addRegisterScript' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '회원가입 form onsubmit 함수 추가(본인 확인 데이터 포함시키기)',
        ],
    ],
    'editUserEnd' => [
        'addEditScript' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '회원정보수정 form onsubmit 함수 추가(본인 확인 데이터 포함시키기)',
        ],
    ],
    'editUserInfo' => [
        'addNameAndHpToEditUserInfo' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '회원정보수정 양식 - 휴대폰 본인 확인 및 성인인증 추가',
        ],
    ],
    'adminUserFormTab' => [
        'addCertTabToManageUserTab' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '관리자 - 회원 관리 양식의 탭에 본인 확인 탭 추가',
        ],
    ],
    'adminUserForm' => [
        'addCertConfigToManageUserForm' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '관리자 - 회원 관리 양식에 본인 확인 추가',
        ],
    ],
    'adminBoardAbilityForm' => [
        'addCertConfigToManageBoardForm' => [
            'module' => 'Certify',
            'priority' => 1,
            'use' => 1,
            'description' => '관리자 - 게시판 관리 양식에 본인 확인 추가',
        ],
    ],
];
