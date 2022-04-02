<?php
require 'database.php';
require './jwt.php';
ob_start();

if (!(isset($GLOBALS['dbConn'])))
    initDB();
$request = $_SERVER['REQUEST_URI'];
try {
    switch ($request) {
        case '/register':
            require __DIR__ . '/register.php';
            break;
        case '/login':
            require __DIR__ . '/login.php';
            break;
        case '/logout':
            require __DIR__ . '/logout.php';
            break;
        case '/newOrder':
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $_ENV['JWTKey']);
            require __DIR__ . '/newOrder.php';
            break;
        case '/orders':
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $_ENV['JWTKey']);
            require __DIR__ . '/orders.php';
            break;
        case '/users':
            $_GET['payload'] = validateJWT($_COOKIE['Authorization'], $_ENV['JWTKey']);
            require __DIR__ . '/users.php';
            break;
        case '':
        case '/':
        case '/index':
            echo "<html>
                <head>
                    <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>
                    <link rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css'>
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
                
                        .input-field input[type=date]:focus+label,
                        .input-field input[type=text]:focus+label,
                        .input-field input[type=email]:focus+label,
                        .input-field input[type=password]:focus+label {
                            color: #e91e63;
                        }
                
                        .input-field input[type=date]:focus,
                        .input-field input[type=text]:focus,
                        .input-field input[type=email]:focus,
                        .input-field input[type=password]:focus {
                            border-bottom: 2px solid #e91e63;
                            box-shadow: none;
                        }
                
                        .custom-link {
                        width: fit-content;
                        border-radius: 12px;
                        margin: 0 8px;
                        margin-top: 64px;
                            background-color: #ec3e9c !important;
                            border: 1px solid rgba(232, 56, 191, 0.85);
                            padding: 8px 32px;
                            height: auto !important;
                            color: white;
                            font-size: 1.5rem; 
                            line-height: inherit !important;
                        }
                        
                        .gay-text {
                            color: #fe9dff;
                            font-weight: 600;
                            font-size: 2rem;
                            letter-spacing: 2;
                            position: fixed;
                            left: 50%;
                            transform: translateX(-50%);
                        }
                    </style>
                </head>
                
                <body>
                
                <canvas id='canvas' style='position : absolute; top : 0px; left : 0px;'></canvas>
                   
                <div class='section'></div>
                <main>
                    <center>
                        <div class='section'></div>
                
                        <h5 class='gay-text'>WELCOME TO THE CLUB BUDDY!</h5>
                        <div class='section'></div>
                
                        <div class='container'>
                            <a href='/login' class='custom-link col s12 btn btn-large waves-effect indigo'>Login</a>
                            <a href='/register' class='custom-link col s12 btn btn-large waves-effect indigo'>Register</a>
                        </div>
                    </center>
                
                    <div class=' section '></div>
                    <div class='section '></div>
                </main>
                
                <script>
                    requestAnimFrame = (function() {
                        return window.requestAnimationFrame ||
                            window.webkitRequestAnimationFrame ||
                            window.mozRequestAnimationFrame ||
                            window.oRequestAnimationFrame ||
                            window.msRequestAnimationFrame ||
                            function(callback) {
                                window.setTimeout(callback, 1000/60);
                            };
                    })();
                    
                    var canvas = document.getElementById('canvas');
                    var ctx = canvas.getContext('2d');
                    
                    var width = 0;
                    var height = 0;
                    
                    window.onresize = function onresize() {
                        width = canvas.width = window.innerWidth;
                        height = canvas.height = window.innerHeight;
                    }
                    
                    window.onresize();
                    
                    var mouse = {
                        X : 0,
                        Y : 0
                    }
                    
                    window.onmousemove = function onmousemove(event) {
                        mouse.X = event.clientX;
                        mouse.Y = event.clientY;
                    }
                    
                    var particules = [];
                    var gouttes = [];
                    var nombrebase = 5;
                    var nombreb = 2;
                    
                    var controls = {
                        rain : 2,
                        Object : 'Nothing',
                        alpha : 1,
                        color : 200,
                        auto : false,
                        opacity : 1,
                        saturation : 100,
                        lightness : 50,
                        back : 100,
                        red : 0,
                        green : 0,
                        blue : 0,
                        multi : false,
                        speed : 2
                    };
                    
                    function Rain(X, Y, nombre) {
                        if (!nombre) {
                            nombre = nombreb;
                        }
                        while (nombre--) {
                            particules.push(
                                {
                                    vitesseX : (Math.random() * 0.25),
                                    vitesseY : (Math.random() * 9) + 1,
                                    X : X,
                                    Y : Y,
                                    alpha : 1,
                                    couleur : 'hsla(' + controls.color + ',' + controls.saturation + '%, ' + controls.lightness + '%,' + controls.opacity + ')',
                                })
                        }
                    }
                    
                    function explosion(X, Y, couleur, nombre) {
                        if (!nombre) {
                            nombre = nombrebase;
                        }
                        while (nombre--) {
                            gouttes.push(
                                {
                                    vitesseX : (Math.random() * 4-2	),
                                    vitesseY : (Math.random() * -4 ),
                                    X : X,
                                    Y : Y,
                                    radius : 0.65 + Math.floor(Math.random() *1.6),
                                    alpha : 1,
                                    couleur : couleur
                                })
                        }
                    }
                    
                    function rendu(ctx) {
                    
                        if (controls.multi == true) {
                            controls.color = Math.random()*360;
                        }
                    
                        ctx.save();
                        ctx.fillStyle = 'rgba('+controls.red+','+controls.green+','+controls.blue+',' + controls.alpha + ')';
                        ctx.fillRect(0, 0, width, height);
                    
                        var particuleslocales = particules;
                        var goutteslocales = gouttes;
                        var tau = Math.PI * 2;
                    
                        for (var i = 0, particulesactives; particulesactives = particuleslocales[i]; i++) {
                    
                            ctx.globalAlpha = particulesactives.alpha;
                            ctx.fillStyle = particulesactives.couleur;
                            ctx.fillRect(particulesactives.X, particulesactives.Y, particulesactives.vitesseY/4, particulesactives.vitesseY);
                        }
                    
                        for (var i = 0, gouttesactives; gouttesactives = goutteslocales[i]; i++) {
                    
                            ctx.globalAlpha = gouttesactives.alpha;
                            ctx.fillStyle = gouttesactives.couleur;
                    
                            ctx.beginPath();
                            ctx.arc(gouttesactives.X, gouttesactives.Y, gouttesactives.radius, 0, tau);
                            ctx.fill();
                        }
                        ctx.strokeStyle = 'white';
                        ctx.lineWidth   = 2;
                    
                        if (controls.Object == 'Umbrella') {
                            ctx.beginPath();
                            ctx.arc(mouse.X, mouse.Y+10, 138, 1 * Math.PI, 2 * Math.PI, false);
                            ctx.lineWidth = 3;
                            ctx.strokeStyle = 'hsla(0,100%,100%,1)';
                            ctx.stroke();
                        }
                        if (controls.Object == 'Cup') {
                            ctx.beginPath();
                            ctx.arc(mouse.X, mouse.Y+10, 143, 1 * Math.PI, 2 * Math.PI, true);
                            ctx.lineWidth = 3;
                            ctx.strokeStyle = 'hsla(0,100%,100%,1)';
                            ctx.stroke();
                        }
                        if (controls.Object == 'Circle') {
                            ctx.beginPath();
                            ctx.arc(mouse.X, mouse.Y+10, 138, 1 * Math.PI, 3 * Math.PI, false);
                            ctx.lineWidth = 3;
                            ctx.strokeStyle = 'hsla(0,100%,100%,1)';
                            ctx.stroke();
                        }
                        ctx.restore();
                    
                        if (controls.auto) {
                            controls.color += controls.speed;
                            if (controls.color >=360) {
                                controls.color = 0;
                            }
                        }
                    }
                    
                    function update() {
                    
                        var particuleslocales = particules;
                        var goutteslocales = gouttes;
                    
                        for (var i = 0, particulesactives; particulesactives = particuleslocales[i]; i++) {
                            particulesactives.X += particulesactives.vitesseX;
                            particulesactives.Y += particulesactives.vitesseY+5;
                            if (particulesactives.Y > height-15) {
                                particuleslocales.splice(i--, 1);
                                explosion(particulesactives.X, particulesactives.Y, particulesactives.couleur);
                            }
                            var umbrella = (particulesactives.X - mouse.X)*(particulesactives.X - mouse.X) + (particulesactives.Y - mouse.Y)*(particulesactives.Y - mouse.Y);
                            if (controls.Object == 'Umbrella') {
                                if (umbrella < 20000 && umbrella > 10000 && particulesactives.Y < mouse.Y) {
                                    explosion(particulesactives.X, particulesactives.Y, particulesactives.couleur);
                                    particuleslocales.splice(i--, 1);
                                }
                            }
                            if (controls.Object == 'Cup') {
                                if (umbrella > 20000 && umbrella < 30000 && particulesactives.X+138 > mouse.X && particulesactives.X-138 < mouse.X && particulesactives.Y > mouse.Y) {
                                    explosion(particulesactives.X, particulesactives.Y, particulesactives.couleur);
                                    particuleslocales.splice(i--, 1);
                                }
                            }
                            if (controls.Object == 'Circle') {
                                if (umbrella < 20000) {
                                    explosion(particulesactives.X, particulesactives.Y, particulesactives.couleur);
                                    particuleslocales.splice(i--, 1);
                                }
                            }
                        }
                    
                        for (var i = 0, gouttesactives; gouttesactives = goutteslocales[i]; i++) {
                            gouttesactives.X += gouttesactives.vitesseX;
                            gouttesactives.Y += gouttesactives.vitesseY;
                            gouttesactives.radius -= 0.075;
                            if (gouttesactives.alpha > 0) {
                                gouttesactives.alpha -= 0.005;
                            } else {
                                gouttesactives.alpha = 0;
                            }
                            if (gouttesactives.radius < 0) {
                                goutteslocales.splice(i--, 1);
                            }
                        }
                    
                        var i = controls.rain;
                        while (i--) {
                            Rain(Math.floor((Math.random()*width)), -15);
                        }
                    }
                    
                    function Screenshot() {
                        window.open(canvas.toDataURL());
                    }
                    
                    (function boucle() {
                        requestAnimFrame(boucle);
                        update();
                        rendu(ctx);
                    })();
                </script>
                <script type='text/javascript ' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js '></script>
                <script type='text/javascript ' src='https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js '></script>
                </body>
            
            </html>";
            break;
        case '/debug':
            require __DIR__ . '/debug.php';
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/404.php';
            break;
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo 'Exception: ', $e->getMessage(), $e->getCode();
}

ob_end_flush();
