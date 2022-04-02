<?php
require 'database.php';
require 'jwt.php';
ob_start();
?>
<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <style>

        .custom-link {
            width: fit-content;
            border-radius: 20px;
            background: #ec3e9c;
            border: 1px solid rgba(232, 56, 191, 0.85);
            color: white;
            font-size: 1.5rem;
        }

        .indigo-text {
            color: #ec3e9c !important;
            font-weight: 600;
            font-size: 2rem;
            letter-spacing: 2;
        }

        label {
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
<div class="section"></div>
<main>
    <center>
        <div class="section"></div>

        <h5 class="indigo-text">TAKE YOUR ORdERS AND GET OUT</h5>
        <div class="section"></div>

        <?php
        if (!(isset($_GET['payload'])))
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $_ENV['JWTKey']);
        $user_id = json_decode($_GET['payload'])->sub;
        $sql = "SELECT cafes.cafe_name, orders.order_name FROM cafes JOIN orders ON cafes.cafe_id = orders.cafe_id WHERE orders.user_id = $1 ORDER BY cafes.cafe_name";
        $query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
        if (!($query)) {
            throw new Exception('myOrders error: wrong header information', 400);
        }
        $result = pg_execute($GLOBALS['dbConn'], "my_query", array($user_id));
        if (!($result))
            throw new Exception("myOrders error: query not found: $query\n", 404);

        while ($row = pg_fetch_row($result)) {
            echo "<div style='display: flex; width: fit-content;
                font-size: 1.2rem;
                font-family: monospace;
                color: #f921e5;'>";
            foreach ($row as $key)
                echo "<p style='margin: 4px 8px'>$key</p>";
            echo "</div>";
        }
        echo "<a href='/newOrder' class='custom-link col s12 btn btn-large waves-effect'>MAKE ONE MORE AGAIN ORDER</a>";
        ?>

    </center>

    <div class=" section "></div>
    <div class="section "></div>
</main>
<script type="text/javascript " src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js "></script>
<script type="text/javascript "
        src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js "></script>
</body>
</html>

<?php ob_end_flush();?>


