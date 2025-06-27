<?php 
require("../config/connection.php");

$query = "CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    trailer_url VARCHAR(255),
    cast TEXT,
    rating VARCHAR(10),
    genre VARCHAR(100),
    release_date DATE,
    is_upcoming BOOLEAN DEFAULT FALSE)";
    
$execute = $mysqli->prepare($query);
$execute->execute();
