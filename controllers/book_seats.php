<?php
// backend/controllers/book_seats.php

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Booking.php';

$input = json_decode(file_get_contents('php://input'), true);
$showtimeId = $input['showtime_id'] ?? null;
$requestedSeats = $input['seats'] ?? null;

if (!$showtimeId || !is_array($requestedSeats)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$userId = $_SESSION['user_id'];

$mysqli->begin_transaction();
try {
    // 1. Re-check availability under lock
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
        $mysqli->rollback();
        http_response_code(409);
        echo json_encode([
        'error' => 'Some seats were just taken',
        'seats' => $unavailable
        ]);
        exit;
    }

    // 2. Mark seats as booked
    if (!Seat::updateStatusBulk($mysqli, $requestedSeats, 'booked')) {
        throw new Exception('Failed to update seat status');
    }

    // 3. Create booking record
    $bookingId = Booking::create($mysqli, $userId, (int)$showtimeId, $requestedSeats);

    $mysqli->commit();
    echo json_encode([
    'booking_id' => $bookingId,
    'message'    => 'Booking confirmed'
    ]);
    
} 
catch (Exception $e) {
    $mysqli->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Booking failed', 'details' => $e->getMessage()]);
    echo json_encode([$showtimeId]);
}
