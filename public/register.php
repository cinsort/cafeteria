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

        <h5 class="indigo-text">FUCK YOUUUU</h5>
        <div class="section"></div>

        <?php
            if (isset($_POST['user_name']) && isset($_POST['password'])) {
                $sql = "INSERT INTO users (user_name, password) VALUES ($1, $2) RETURNING user_id";
                $query = pg_prepare($GLOBALS['dbConn'], "my_query", $sql);
                if (!($query)) {
                    throw new Exception('register error: wrong header information', 400);
                }
                $result = pg_execute($GLOBALS['dbConn'], "my_query", array($_POST['user_name'], $_POST['password']));
                if (!($result))
                    throw new Exception("register error: query failed: $query\n", 409);
                $user_id = pg_fetch_row($result)['0'];

                $payload = [
                    'exp' => time() + 7200,
                    'sub' => $user_id,
                ];
                $token = encodeJWT($payload, $_ENV['JWTKey']);
                setcookie('Authorization', $token);
                echo "<a href='/newOrder' class='custom-link col s12 btn btn-large waves-effect'>MAKE A BRAND NEW ORDER</a>";
            } else {
                echo "<form class='container' action='register.php' method='post'>
                    <label for='first'>Login</label>
                    <input id='first' type='text' name='user_name' class='container-input'>
                    <label for='second'>Password</label> 
                    <input id='second' type='password' name='password' class='container-input'>
                    <button type='submit' class='container-button'>Register</button>
                </form>";
            }
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

