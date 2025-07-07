<?php
require(__DIR__ . "/../models/Showtime.php");
require_once(__DIR__ . "/../config/connection.php");
require(__DIR__ . "/../services/MovieService.php");
require(__DIR__ . "/../services/ResponseService.php");

class ShowtimeController{
    
    public static function getShowtime(){
    global $mysqli;
    header('Content-Type: application/json');

// if (empty($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Not authenticated']);
//     exit;
// }

        global $mysqli;
        //if no id return all
        if (!isset($_GET["id"])) {
            
            $movies = Showtime::all($mysqli);
            $movies_array = MovieService::moviesToArray($movies);
            // echo ResponseService::success_response($movies_array);
            echo json_encode($movies_array);
            
            return;
        }
        //custom id
            $id = $_GET["id"];
            $movies = Showtime::find($mysqli, $id)->toArray();
            echo ResponseService::success_response($movies);
            return;
    }
}