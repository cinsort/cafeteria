<?php
require 'jwt.php';
require 'database.php';

if (!(isset($_GET['payload'])))
    $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $_ENV['JWTKey']);
$user_id = json_decode($_GET['payload'])->sub;
$result = pg_query($GLOBALS['dbConn'], "SELECT user_id, user_name FROM users ORDER BY user_id");
if (!($result))
    throw new Exception("users error: query not found", 404);

while ($row = pg_fetch_row($result)) {
    echo "<div style='display: flex; width: fit-content;
        font-size: 1.2rem;
        font-family: monospace;
        color: #f921e5;'>";
    foreach ($row as $key)
        echo "<p style='margin: 4px 8px'>$key</p>";
    echo "</div>";
}