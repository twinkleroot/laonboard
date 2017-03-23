<?php
// echo preg_match('/^([A-Za-z0-9]+){6,}$/', 'Dasdsadsad1');
// echo preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[~!@#$%^&*()\-_=+]).{6,}/', 'Sasdsadase1!');
// echo preg_match('/(?=.*[a-z]).{6,}/', 'Sasdsadase1!123');
// echo preg_match('/([A-Za-z0-9]).(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/', 'Sasdsadase1');
// if(preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])^(012)|^(123)|^(234)|^(345)|^(456)|^(567)|^(678)|^(789)|^(890)|^(901)/',
//             'Dkswjdah1')) {
//     echo '문자열에 연속된 숫자가 포함되어 있지 않습니다.';
// } else {
//     echo '연속된 숫자는 금지입니다.';
// }
if(preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(012|123|234|345|456|567|678|789|890|901)/', 'Sasdsadase13')) {
    echo 'true';
} else {
    echo 'false';
}

?>
