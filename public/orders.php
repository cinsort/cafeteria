<?php
if (!(isset($_GET['payload'])))
    throw new Exception ('orders error: invalid token', 401);

$user_id = json_decode($_GET['payload'])->sub;
$sql = "SELECT cafes.cafe_name, orders.order_name FROM cafes JOIN orders ON cafes.cafe_id = orders.cafe_id WHERE orders.user_id = $1 ORDER BY cafes.cafe_name";
$query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
$result = pg_execute($GLOBALS['dbConn'], "my_query", array($user_id));
if (!($result))
    throw new Exception("newOrder error: cafe not found: $query\n", 404);

while ($row = pg_fetch_row($result)) {
    echo "<div style='display: flex; width: fit-content;
        font-size: 1.2rem;
        font-family: monospace;
        color: #f921e5;'>";
    foreach ($row as $key)
        echo "<p style='margin: 4px 8px'>$key</p>";
    echo "</div>";
}
http_response_code(201);