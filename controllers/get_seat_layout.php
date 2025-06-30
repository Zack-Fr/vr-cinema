<?php
// backend/controllers/get_seat_layout.php

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Seat.php';

$showtimeId = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : null;
if (!$showtimeId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing showtime_id']);
    exit;
}

$seats = Seat::allByShowtime($mysqli, $showtimeId);
$data  = array_map(fn($s) => $s->toArray(), $seats);

echo json_encode(['seats' => $data]);
