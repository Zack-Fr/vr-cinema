<?php
$servername = "localhost";
$username = "root";
$pass = "sqlworkbench";
$dbname = "cinema_db";

$mysqli = new mysqli($servername, $username, $pass, $dbname);

if ($mysqli->connect_error) {
    die("connection failed" . $connection->connect_error);

// } else{

    // echo ("connected");
}
    ?>