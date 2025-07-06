<?php
require(__DIR__ . "/../models/Showtime.php");
require_once(__DIR__ . "/../config/connection.php");
require(__DIR__ . "/../services/MovieService.php");
require(__DIR__ . "/../services/ResponseService.php");

class ShowtimeController{
    public static function getShowtime(){
    global $mysqli;
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Showtime.php';

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$movieId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing movie_id']);
    exit;
}

$showtimes = Showtime::allByMovie($mysqli, $movieId);
$data = array_map(fn($st) => $st->toArray(), $showtimes);

echo json_encode(['showtimes' => $data]);
}
}