<?php
$dbConn = pg_connect('host=postgres dbname=app user=postgres password=password')
    or die('Cannot connect to DB: ' . pg_last_error());
$dbInfo = pg_version($dbConn);

pg_query($dbConn,
    "CREATE TABLE IF NOT EXISTS 
    users(
        id INTEGER PRIMARY KEY,
        name VARCHAR(25),
    password VARCHAR(100))"
) or die("Cannot create users table");
