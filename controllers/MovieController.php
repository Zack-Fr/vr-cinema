<?php
require(__DIR__ . "/../models/Movie.php");
require_once(__DIR__ . "/../config/connection.php");
require(__DIR__ . "/../services/MovieService.php");
require(__DIR__ . "/../services/ResponseService.php");

class MovieController
{
    public static function getAllMovies()
    {
        global $mysqli;
        //if no id return all
        if (!isset($_GET["id"])) {
            
            $movies = Movie::all($mysqli);
            $movies_array = MovieService::moviesToArray($movies);
            // echo ResponseService::success_response($movies_array);
            echo json_encode($movies_array);
            
            return;
        }
        //custom id
        $id = $_GET["id"];
        $movies = Movie::find($mysqli, $id)->toArray();
        echo ResponseService::success_response($movies);
        return;
    }
}