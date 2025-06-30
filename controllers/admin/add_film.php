<?php


session_start();
header('Content-Type: application/json');

// 1. Auth check
if (empty($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// 2. Decode & validate input
$input = json_decode(file_get_contents('php://input'), true);
$title       = trim($input['title']       ?? '');
$cast        = trim($input['cast']        ?? '');
$genre       = trim($input['genre']       ?? '');
$rating      = trim($input['rating']      ?? '');
$isUpcoming  = isset($input['is_upcoming']) 
            ? (int)$input['is_upcoming'] 
            : null;
$releaseDate = trim($input['release_date'] ?? '');
$description = trim($input['description'] ?? '');

if (!$title || !$cast || !$genre || !$rating
    || $isUpcoming === null || !$releaseDate || !$description
) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required film fields']);
    exit;
}

// 3. Bootstrap DB + Model
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Movies.php';

try {
    // 4. Create the movie
    $newId = Movies::create($mysqli, [
        'title'        => $title,
        'cast'         => $cast,
        'genre'        => $genre,
        'rating'       => $rating,
        'is_upcoming'  => $isUpcoming,
        'release_date' => $releaseDate,
        'description'  => $description
    ]);

    // 5. Respond success
    http_response_code(201);
    echo json_encode([
    'status'   => 'success',
    'movie_id' => $newId,
    'message'  => 'Film added successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
    'error'   => 'Could not add film',
    'details' => $e->getMessage()
    ]);
}
