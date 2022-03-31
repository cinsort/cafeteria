<?php
if (isset($_COOKIE['Authorization'])) {
    setcookie('Authorization', '', time()-(60*60*24*7));
    unset($_COOKIE['Authorization']);
}
http_response_code(204);
//header('Location: ' .
//    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on') ? 'https' : 'http') .
//    '://'.
//    $_SERVER['HTTP_HOST'].
//    '/index'
//);
