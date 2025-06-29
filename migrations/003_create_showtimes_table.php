<?php 
require("../config/connection.php");

$query = "CREATE TABLE `showtimes` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`movie_id` INT UNSIGNED NOT NULL,
`start_time` DATETIME NOT NULL,
`created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
INDEX `idx_showtimes_movie` (`movie_id`),
CONSTRAINT `fk_showtimes_movies`
    FOREIGN KEY (`movie_id`)
    REFERENCES `movies` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
$execute = $mysqli->prepare($query);
$execute->execute();

