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

            .input-field input[type=date]:focus + label,
            .input-field input[type=text]:focus + label,
            .input-field input[type=email]:focus + label,
            .input-field input[type=password]:focus + label {
                color: #e91e63;
            }

            .input-field input[type=date]:focus,
            .input-field input[type=text]:focus,
            .input-field input[type=email]:focus,
            .input-field input[type=password]:focus {
                border-bottom: 2px solid #e91e63;
                box-shadow: none;
            }

            .container {
                display: flex;
                width: 400px;
                margin: 0 auto;
                justify-content: center;
                flex-direction: column;
                align-items: center;
            }

            .container-input {
                width: 200px;
                border-radius: 4px;
            }

            .container-button {
                width: fit-content;
                border-radius: 20px;
                background: #ec3e9c;
                border: 1px solid rgba(232, 56, 191, 0.85);
                padding: 8px 32px;
                color: white;
                font-size: 1.5rem;
            }

            .back-btn {
                width: fit-content;
                border-radius: 20px;
                background: #ec3e9c;
                border: 1px solid rgba(232, 56, 191, 0.85);
                padding: 8px 32px;
                color: white;
                font-size: 1.5rem;
            }

            .indigo-text {
                color: #ec3e9c !important;
                font-weight: 600;
                font-size: 2rem;
                letter-spacing: 2;
            }

            .custom-link {
                width: fit-content;
                border-radius: 20px;
                background: #ec3e9c;
                border: 1px solid rgba(232, 56, 191, 0.85);
                color: white;
                font-size: 1.5rem;
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

            <h5 class="indigo-text">Nice to meet you sir</h5>
            <div class="section"></div>

            <?php
            if (isset($_POST['user_name']) && isset($_POST['password'])) {
                $sql = "SELECT * FROM users WHERE (user_name = $1) ORDER BY user_id ASC FETCH FIRST ROW ONLY";
                $sqlName = "loginUserQuery";
                $query = pg_prepare($GLOBALS['dbConn'], $sqlName, $sql);
                if (!$query) {
                    http_response_code(400);
                    echo (" <p >MAN SIGN UP</p>
                        <a href='index' class='back-btn'>GO BACK</a>");
                    exit();
                }
                $result = pg_execute($GLOBALS['dbConn'], $sqlName, array($_POST['user_name']));
                $row = pg_fetch_all($result);
                if (!isset($row['0'])) {
                    http_response_code(401);
                    echo (" <p >MAN SIGN UP</p>
                        <a href='index' class='back-btn'>GO BACK</a>");
                    exit();
                }
                $payload = [
                    'exp' => time() + 7200,
                    'sub' => $row['0']['user_id'],
                ];
                $token = encodeJWT($payload, $_ENV['JWTKey']);
                setcookie('Authorization', $token);
                http_response_code(200);
                echo "<a href='/myOrders' class='custom-link col s12 btn btn-large waves-effect'>MY ORDERS</a>";
            } else {
                echo "<form class='container' action='/login.php' method='post'>
                    <label for='first'>Login</label>
                    <input id='first' type='text' name='user_name' class='container-input'>
                    <label for='second'>Password</label>
                    <input id='second' type='password' name='password' class='container-input'>
                    <button type='submit' class='container-button'>Authorize</button>
                </form>";
            }
            ?>
        </center>
        <div class=" section "></div>
        <div class="section "></div>
    </main>
    <script type="text/javascript " src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js "></script>
    <script type="text/javascript " src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js "></script>
    </body>
</html>

<?php ob_end_flush();?>