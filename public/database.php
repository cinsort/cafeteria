<?php
$GLOBALS['dbConn'] = pg_connect('host=postgres dbname=app user=postgres password=password')
    or die('Cannot connect to DB: ' . pg_last_error());
//$dbInfo = pg_version($GLOBALS['dbConn']);

if(!function_exists('initDB')) {
    function initDB(): void
    {
        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS users(
            user_id SERIAL PRIMARY KEY,
            user_name VARCHAR(25) NOT NULL,
            password VARCHAR(100) NOT NULL
        )") or die("Error creating users table");

        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS cafes(
            cafe_id SERIAL PRIMARY KEY,
            cafe_name VARCHAR(25) UNIQUE NOT NULL
        )") or die("Error creating cafes table");

        pg_query($GLOBALS['dbConn'], "CREATE TABLE IF NOT EXISTS orders(
            order_id SERIAL PRIMARY KEY,
            order_name VARCHAR(25) NOT NULL,
            cafe_id INT,
            CONSTRAINT fk_cafes
                FOREIGN KEY(cafe_id)
                    REFERENCES cafes(cafe_id)
        )") or die("Error creating orders table");

        $res = pg_query($GLOBALS['dbConn'], "SELECT * FROM cafes");
        if (pg_num_rows($res) < 3)
            pg_query($GLOBALS['dbConn'], "INSERT INTO cafes(cafe_name)
            VALUES
                ('NONE OF YOUR BUSINESS'),
                ('BERRY - RASPBERRY'),
                ('PALKI')
            ") or die("Error inserting cafe_name into cafes table");
    }
}
//$data = pg_query($GLOBALS['dbConn'],"DELETE FROM cafes
//WHERE cafe_id =2;
//") or die("Error creating orders table");
//
//$data = pg_query($GLOBALS['dbConn'],"SELECT * FROM cafes WHERE cafe_id > 2;
//") or die("Error creating orders table");
//echo $data;

