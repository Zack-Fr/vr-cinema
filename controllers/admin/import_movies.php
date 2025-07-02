<?php
// backend/controllers/admin/import_movies.php

session_start();
header('Content-Type: application/json');

// 1. Auth check
// if (empty($_SESSION['user_id']) || !$_SESSION['is_admin']) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Forbidden']);
//     exit;
// }

// 2. Decode & validate
$input = json_decode(file_get_contents('php://input'), true);
$movies = $input['movies'] ?? null;
if (!is_array($movies) || empty($movies)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload: expected "movies" array']);
    exit;
}

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Movies.php';
require_once __DIR__ . '/../../models/Showtime.php';

$result = [];
try {
    foreach ($movies as $m) {
        // 3. Create movie
        $movieData = [
        'title'        => $m['title']        ?? '',
        'cast'         => $m['cast']         ?? '',
        'genre'        => $m['genre']        ?? '',
        'rating'       => $m['rating']       ?? '',
        'is_upcoming'  => (int)($m['is_upcoming'] ?? 0),
        'release_date' => $m['release_date'] ?? '',
        'description'  => $m['description']  ?? '',
        ];
        $movieId = Movies::create($mysqli, $movieData);

        // 4. Bulk‐import showtimes if provided
        $createdShowtimes = [];
        if (!empty($m['showtimes']) && is_array($m['showtimes'])) {
            $createdShowtimes = Showtime::bulkCreate($mysqli, $movieId, $m['showtimes']);
        }

        $result[] = [
        'movie_id'       => $movieId,
        'showtime_ids'   => $createdShowtimes
        ];
    }

    // 5. Respond
    http_response_code(201);
    echo json_encode([
    'status'  => 'success',
    'imported'=> $result
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
    'error'   => 'Import failed',
    'details' => $e->getMessage()
    ]);
}
