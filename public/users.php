<?php
if (!(isset($_GET['payload'])))
    throw new Exception ('users error: invalid token ', 401);

$user_id = json_decode($_GET['payload'])->sub;
$result = pg_query($GLOBALS['dbConn'], "SELECT user_id, user_name FROM users ORDER BY user_id");
if (!($result))
    throw new Exception("users error: cafe not found: $query\n", 404);

while ($row = pg_fetch_row($result)) {
    foreach ($row as $key) echo $key;
}