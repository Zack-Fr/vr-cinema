<?php


session_start();
header('Content-Type: application/json');

// 1. Auth check
// if (empty($_SESSION['user_id'])  || !$_SESSION['is_admin']) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Not authenticated']);
//     exit;
// }

// 2. Decode & validate input
$input = json_decode(file_get_contents('php://input'), true);
$movieId   = isset($input['movie_id'])   ? (int)$input['movie_id']   : null;
$showtimes = isset($input['showtimes'])  ? $input['showtimes']        : null;

if (!$movieId || !is_array($showtimes) || empty($showtimes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload: require movie_id & showtimes array']);
    exit;
}

// 3. Bootstrap DB & Model
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Showtime.php';

try {
    // 4. Bulk insert
    $newIds = Showtime::bulkCreate($mysqli, $movieId, $showtimes);

    // 5. Respond with inserted IDs
    http_response_code(201);
    echo json_encode([
    'status'       => 'success',
    'inserted_ids' => $newIds,
    'count'        => count($newIds)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
    'error'   => 'Failed to import showtimes',
    'details' => $e->getMessage()
    ]);
}
