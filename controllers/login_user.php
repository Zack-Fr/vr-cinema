
<?php

session_start();
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../config/connection.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Email and password required']);
    exit;
}
$user = User::verifyCredentials($mysqli, $input['email'], $input['password']);
if (!$user) {
    http_response_code(401);
    echo json_encode(['error'=>'Invalid credentials']);
    exit;
}
// successful login
$_SESSION['user_id'] = $user->toArray()['id'];
echo json_encode(['status'=>200,'user'=>$user->toArray()]); 