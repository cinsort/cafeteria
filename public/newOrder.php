<?php
if (!(isset($_GET['payload'])))
    throw new Exception ('newOrder error: invalid token', 401);
if (!(isset($_POST['order_name']) && isset($_POST['cafe_name'])))
    throw new Exception ('newOrder error: invalid argument', 400);

$user_id = json_decode($_GET['payload'])->sub;
$sql = "SELECT * FROM cafes WHERE cafe_name = $1 LIMIT 1";
$query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
$result = pg_execute($GLOBALS['dbConn'], "my_query", array($_POST['cafe_name']));
if (!($result))
    throw new Exception("newOrder error: cafe not found: $query\n", 404);
$cafe_id = pg_fetch_row($result)['0'];

$sql = "INSERT INTO orders(order_name, cafe_id, user_id) VALUES ($1, $2, $3)";
$query = pg_prepare($GLOBALS['dbConn'], "insert_order", $sql);
$result = pg_execute($GLOBALS['dbConn'], "insert_order", array($_POST['order_name'], $cafe_id, $user_id));
if (!($result))
    throw new Exception("newOrder error: inserting failed: $query\n", 409);
header('Content-Type: application/json; charset=utf-8');
http_response_code(201);