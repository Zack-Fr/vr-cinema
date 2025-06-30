<?php
// backend/controllers/check_seat_availability.php

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Seat.php';

$input = json_decode(file_get_contents('php://input'), true);
$showtimeId = $input['showtime_id'] ?? null;
$requestedSeats = $input['seats'] ?? null;

if (!$showtimeId || !is_array($requestedSeats)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Fetch current statuses
$allSeats = Seat::allByShowtime($mysqli, (int)$showtimeId);
$statusMap = [];
foreach ($allSeats as $s) {
    $statusMap[$s->id] = $s->status;
}

$unavailable = [];
foreach ($requestedSeats as $seatId) {
    if (!isset($statusMap[$seatId]) || $statusMap[$seatId] !== 'available') {
        $unavailable[] = $seatId;
    }
}

if ($unavailable) {
    http_response_code(409);
    echo json_encode([
    'error' => 'Some seats are no longer available',
    'seats' => $unavailable
    ]);
    exit;
}

// All good
echo json_encode(['available' => true]);
