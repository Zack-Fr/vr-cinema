<?php 
require("../config/connection.php");


$query = "CREATE TABLE users(
        id INT(11) AUTO_INCREMENT PRIMARY KEY, 
        name VARCHAR(100) NOT NULL, 
        email VARCHAR(255) NOT NULL, 
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";

$execute = $mysqli->prepare($query);
$execute->execute();