<?php
require "./database.php";
require "./jwt.php";
$GLOBALS['JWTKey'] = 'eAEXWnD2bHbcoxZxC6v1jnvzJgD7lNhygGqInMaq6njwg59Qgj0EK80Oyh7jzHpx';
if (!(isset($GLOBALS['dbConn']))) initDB();

$request = $_SERVER['REQUEST_URI'];
try {
    switch ($request) {
        case '/register':
            require __DIR__ . '/register.php';
            break;
        case '/login':
            require __DIR__ . '/login.php';
            break;
        case '/logout':
            require __DIR__ . '/logout.php';
            break;
        case '/newOrder':
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $GLOBALS['JWTKey']);
            require __DIR__ . '/newOrder.php';
            break;
        case '/orders':
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $GLOBALS['JWTKey']);
            require __DIR__ . '/orders.php';
            break;
        case '':
        case '/':
        case '/index':
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/404.php';
            break;
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo 'Exception: ', $e->getMessage(), $e->getCode();
}