<?php

return [
    'captchaPlace' => [
        'addRecaptchaClient' => [
            'module' => 'GoogleRecaptcha',
            'priority' => 1,
            'use' => 1,
            'description' => '자동 등록 방지로 구글 리캡챠(Google Invisible Recaptcha)를 사용하는 모듈',
        ],
    ],
];
