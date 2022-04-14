<?php
require "./jwt.php";
$GLOBALS['dbConn'] = pg_connect('host=postgres dbname=app user=postgres password=password')
    or die('database error: cannot connect to DB: ' . pg_last_error());

if(!function_exists('initDB')) {
    function initDB(): void
    {
        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS users(
            user_id SERIAL PRIMARY KEY,
            user_name VARCHAR(25) NOT NULL,
            password VARCHAR(100) NOT NULL
        )") or die("database error: error creating users table");

        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS cafes(
            cafe_id SERIAL PRIMARY KEY,
            cafe_name VARCHAR(25) UNIQUE NOT NULL
        )") or die("database error: error creating cafes table");

        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS orders(
            order_id SERIAL PRIMARY KEY,
            order_name VARCHAR(255) NOT NULL,
            cafe_id INT REFERENCES cafes(cafe_id) NOT NULL,
            user_id INT REFERENCES users(user_id) NOT NULL
        )") or die("database error: error creating orders table");

        $res = pg_query($GLOBALS['dbConn'], "SELECT * FROM cafes");
        if (pg_num_rows($res) < 3)
            pg_query($GLOBALS['dbConn'], "INSERT INTO cafes(cafe_name)
            VALUES
                ('NONE OF YOUR BUSINESS'),
                ('BERRY - RASPBERRY'),
                ('PALKI')
            ") or die("database error: error inserting cafe_name into cafes table");
    }
}