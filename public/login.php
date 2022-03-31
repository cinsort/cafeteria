<?php
if (!(isset($_POST['user_name']) && isset($_POST['password'])))
    throw new Exception('register error: invalid parameters', 401);
$sql = "SELECT * FROM users WHERE user_name = $1 AND password = $2 ORDER BY user_id DESC LIMIT 1";
$query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
$result = pg_execute($GLOBALS['dbConn'], "my_query", array($_POST['user_name'], $_POST['password']));
if (!($result))
    throw new Exception('login error: user not found', 404);
$user_id = pg_fetch_row($result)['0'];
$payload = [
    'exp' => time() + 7200,
    'sub' => $user_id,
];
$token = encodeJWT($payload, $GLOBALS['JWTKey']);
header('Content-Type: application/json; charset=utf-8');
http_response_code(201);
setcookie('Authorization', $token);