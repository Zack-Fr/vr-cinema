
<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Users.php';

// Get raw POST data
$input = json_decode(file_get_contents('php://input'), true);

// Basic validation
$errors = [];
if (empty($input['username'])) {
    $errors['username'] = 'Username is required.';
}
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Valid email is required.';
}
if (empty($input['password']) || strlen($input['password']) < 6) {
    $errors['password'] = 'Password must be at least 6 characters.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Hash the password
$input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);

// Create user
$usersModel = new Users($pdo);
try {
    $newId = $usersModel->create($input);
    http_response_code(201);
    echo json_encode(['message' => 'User registered successfully', 'user_id' => $newId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed']);
}
