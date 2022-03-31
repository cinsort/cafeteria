<?php
if (!(isset($_POST['user_name']) && isset($_POST['password'])))
    throw new Exception('Register error: invalid parameters', 409);
$sql = "INSERT INTO users (user_name, password) VALUES ($1, $2) RETURNING user_id";
$query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
$result = pg_execute($GLOBALS['dbConn'], "my_query", array($_POST['user_name'], $_POST['password']));
if (!($result))
    throw new Exception('Register error: inserting failed', 409);
$user_id = pg_fetch_row($result)['0'];

$payload = [
    'exp' => time() + 7200,
    'sub' => $user_id,
];
$token = encodeJWT($payload, $GLOBALS['JWTKey']);
header('Content-Type: application/json; charset=utf-8');
http_response_code(201);
setcookie('Authorization', $token);
