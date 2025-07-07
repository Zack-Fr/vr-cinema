<?php
// backend/controllers/admin/configure_seat_layout.php

session_start();
header('Content-Type: application/json');

// 1. Admin check (reuse your is_admin session flag)
// if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Forbidden']);
    
//     exit;
// }

// 2. Decode & validate payload
$input       = json_decode(file_get_contents('php://input'), true);
$showtimeId  = isset($input['showtime_id']) ? (int)$input['showtime_id'] : null;
$seats       = isset($input['seats']) && is_array($input['seats']) ? $input['seats'] : null;

if (!$showtimeId || !$seats) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload: require showtime_id and seats array']);
    exit;
}

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../models/Seat.php';

try {
    // 3. Bulk create seats
    $newIds = Seat::bulkCreate($mysqli, $showtimeId, $seats);

    // 4. Respond
    http_response_code(201);
    echo json_encode([
    'status'      => 'success',
    'inserted_ids'=> $newIds,
    'count'       => count($newIds)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
    'error'   => 'Failed to configure seat layout',
    'details' => $e->getMessage()
    ]);
}
