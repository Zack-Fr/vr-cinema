<?php
require __DIR__ . '/../config/connection.php';
// This block is used to extract the route name from the URL
//----------------------------------------------------------
// Define your base directory 
$base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base directory from the request if present
if (strpos($request, $base_dir) === 0) {
    $request = substr($request, strlen($base_dir));
}

// Ensure the request is at least '/'
if ($request == '') {
    $request = '/';
}
$method = $_SERVER['REQUEST_METHOD'];


$apis = [
    '/get_movies' => ['controller' => 'MovieController', 'method' => 'getAllMovies'],

    '/get_showtime' => ['controller' => 'ShowtimeController', 'method' => 'getShowtime'],


    //===============================users

    '/login_user'         => ['controller' => 'AuthController', 'method' => 'loginUser'],
    '/register_user'         => ['controller' => 'AuthController', 'method' => 'registerUser'],
    '/logout_user'         => ['controller' => 'AuthController', 'method' => 'logoutUser'],
    '/get_user'         => ['controller' => 'AuthController', 'method' => 'getUser'],
    
    //=====================================admin
    '/add_film'         => ['controller' => 'AdminController', 'method' => 'addFilm'],


];

//Routing Logic here 
//This is a dynamic logic, that works on any array... 
//----------------------------------------------------------

if (isset($apis[$request])) {
    $controller_name = $apis[$request]['controller']; //if $request == /articles, then the $controller_name will be "ArticleController" 
    $method = $apis[$request]['method'];
    // echo $controller_name;
    require_once "../controllers/{$controller_name}.php";

    $controller = new $controller_name();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        echo "Error: Method {$method} not found in {$controller_name}.";
    }
} else {
    echo "404 Not Found";
    echo "$request";
}
