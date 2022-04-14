<?php
require 'database.php';
ob_start();
?>

<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        body {
            background: #fff;
        }

        .cool-class {
            color: #ec3e9c;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
<div class="section"></div>
<main>
    <center>
        <div class="section"></div>

        <h5 class="indigo-text">Nice to meet you sir</h5>
        <div class="section"></div>

        <p class="cool-class">ПОделом!!!!!</p>

        <?php
            if (isset($_COOKIE['Authorization'])) {
                setcookie('Authorization', '', time() - (60 * 60 * 24 * 7));
                unset($_COOKIE['Authorization']);
            }
            http_response_code(204);
            echo "logout: successfully logged out";
        ?>

    </center>

    <div class=" section "></div>
    <div class="section "></div>
</main>
<script type="text/javascript " src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js "></script>
<script type="text/javascript " src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js "></script>
</body>

</html>

<?php
ob_end_flush();

?>