<?php
session_start();
header('Content-Type: application/json');
require_once '../config/connection.php';
require_once '../models/Users.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'Not authenticated']);
    exit;
}
$userId = $_SESSION['user_id'];
// GET vs PUT handling
if ($_SERVER['REQUEST_METHOD']==='GET') {
    $user = User::find($mysqli, $userId);
    echo json_encode(['user'=>$user->toArray()]);
    exit;
}
if ($_SERVER['REQUEST_METHOD']==='PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    /** @var User $user */
    $user = User::find($mysqli, $userId);

    if (isset($input['mobile_num'])) {
    $user->setMobileNum($input['mobile_num']);
}
    if (! $user->update($mysqli)) {
    http_response_code(500);
    echo json_encode(['error'=>'Could not save user']);
    exit;
}
    echo json_encode([
        'status'=>200,
        'user'=>$user->toArray()]);
    exit;
}