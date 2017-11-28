<?php
 return array (

    // 상수
    'IP_DISPLAY' => '\\1.♡.\\3.\\4',
    'VER' => Carbon\Carbon::now()->format('Ymd').'-1',
    'URL_REGEX' => '/(http(s)?\:\/\/)?[0-9a-zA-Z]+([\.\-]+[0-9a-zA-Z]+)*(:[0-9]+)?(\/?(\/[\.\w]*)+)?(([\?\&\=][\w]+)+)?/',

    // 홈페이지 기본 환경 설정
    'title' => '라온보드',
    'superAdmin' => 'admin@admin.com',
    'usePoint' => 1,
    'loginPoint' => 100,
    'memoSendPoint' => 0,
    'openDate' => 0,
    'newDel' => 30,
    'memoDel' => 180,
    'popularDel' => 180,
    'newRows' => 15,
    'pageRows' => 10,
    // 'mobilePageRows' => 10,
    // 'writePages' => 10,
    // 'mobilePages' => 5,
    'newSkin' => 'default',
    'searchSkin' => 'default',
    'useCopyLog' => 1,
    'pointTerm' => 0,
    'analytics' => '',
    'addMeta' => '',
    // 게시판 기본
    'linkTarget' => '_blank',
    'readPoint' => 0,
    'writePoint' => 0,
    'commentPoint' => 0,
    'downloadPoint' => 0,
    // 'searchPart' => 10000,
    'imageExtension' => 'gif|jpg|jpeg|png',
    'flashExtension' => 'swf',
    'movieExtension' => 'asx|asf|wmv|wma|mpg|mpeg|mov|avi|mp3',
    'filter' => array (
    0 => '18아,18놈,18새끼,18뇬,18노,18것,18넘,개년,개놈,개뇬,개새,개색끼,개세끼,개세이,개쉐이,개쉑,개쉽,개시키,개자식,개좆,게색기,게색끼,광뇬,뇬,눈깔,뉘미럴,니귀미,니기미,니미,도촬,되질래,뒈져라,뒈진다,디져라,디진다,디질래,병쉰,병신,뻐큐,뻑큐,뽁큐,삐리넷,새꺄,쉬발,쉬밸,쉬팔,쉽알,스패킹,스팽,시벌,시부랄,시부럴,시부리,시불,시브랄,시팍,시팔,시펄,실밸,십8,십쌔,십창,싶알,쌉년,썅놈,쌔끼,쌩쑈,썅,써벌,썩을년,쎄꺄,쎄엑,쓰바,쓰발,쓰벌,쓰팔,씨8,씨댕,씨바,씨발,씨뱅,씨봉알,씨부랄,씨부럴,씨부렁,씨부리,씨불,씨브랄,씨빠,씨빨,씨뽀랄,씨팍,씨팔,씨펄,씹,아가리,아갈이,엄창,접년,잡놈,재랄,저주글,조까,조빠,조쟁이,조지냐,조진다,조질래,존나,존니,좀물,좁년,좃,좆,좇,쥐랄,쥐롤,쥬디,지랄,지럴,지롤,지미랄,쫍빱,凸,퍽큐,뻑큐,빠큐,ㅅㅂㄹㅁ',
    ),

    // 회원 가입
    'userSkin' => 'default',
    'nickDate' => 30,
    'name' => 0,
    'homepage' => 0,
    'tel' => 0,
    'hp' => 0,
    'addr' => 0,
    'signature' => 0,
    'profile' => 0,
    'joinLevel' => 2,
    'joinPoint' => 1000,
    'leaveDay' => 30,
    'useMemberIcon' => 2,
    'iconLevel' => 2,
    'memberIconSize' => 5000,
    'memberIconWidth' => 22,
    'memberIconHeight' => 22,
    'recommend' => 0,
    'recommendPoint' => '2000',
    'loginPoint' => '100',
    'banId' => array (
    0 => 'admin,administrator,관리자,운영자,어드민,주인장,webmaster,웹마스터,sysop,시삽,시샵,manager,매니저,메니저,root,루트,su,guest,방문객',
    ),
    'stipulation' => '해당 홈페이지에 맞는 회원약관을 입력합니다.',
    'privacy' => '해당 홈페이지에 맞는 개인정보처리방침을 입력합니다.',
    'passwordPolicyDigits' => 6,
    'passwordPolicySpecial' => 0,
    'passwordPolicyUpper' => 0,
    'passwordPolicyNumber' => 0,

    // 기본 메일 환경 설정
    'emailUse' => 1,
    'adminEmail' => 'admin@laonboard.com',
    'adminEmailName' => '라온보드',
    'emailCertify' => 0,
    'formmailIsMember' => 1,

    // 게시판 글 작성 시 메일 설정
    'emailWriteSuperAdmin' => 0,
    'emailWriteGroupAdmin' => 0,
    'emailWriteBoardAdmin' => 0,
    'emailWriter' => 0,
    'emailAllCommenter' => 0,

    // 회원가입 시 메일 설정
    'emailJoinSuperAdmin' => 0,
    'emailJoinUser' => 0,

    // 테마설정
    'theme' => 'default',

    // 개별 스킨 설정
    'layoutSkin' => 'default',
    'boardSkin' => 'default',
    'latestSkin' => 'default',

    // 캡챠 확인할 오늘 남긴 글 갯수
    'todayWriteCount' => 20,

    // 게시판 추가 기본 값
    'use_secret' => 0,
    'count_modify' => 0,
    'count_delete' => 1,
    'new' => 24,
    'hot' => 100,
    'upload_count' => 2,
    'upload_size' => 1048576,
    'reply_order' => 1,
    'use_search' => 1,
    'board_skin' => 'default',
    'layout' => 'basic',
    'content_head' => '',
    'content_tail' => '',
    'insert_content' => '',
    'subject_len' => [
        'default' => 60,
        'gallery' => 50,
        'main' => 15,
    ],
    'gallery_height' => 150,
    'image_width' => 600,

) ;
