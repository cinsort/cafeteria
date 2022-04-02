<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <style>
        body {
            overflow: hidden;
        }

        .back-error {
            height: 100vh;
            width: 100vw;
            background-image: url("./background.jpg");
            background-size: 0.1%;
        }

    </style>
</head>
<body>
<div class="back-error">

</div>

<script>
    const backError = document.querySelector('.back-error');
    const body = document.querySelector('body')

    body.addEventListener('mouseenter', (e) => {
        setInterval(() => {
            let first = Math.random();
            let second = Math.random();
            backError.style.transform = `translate(${first * 200 - 100}px,${second * 200 - 100}px)`;
        });
    })
</script>
</body>
</html>

<?php
    http_response_code(404);
?>
