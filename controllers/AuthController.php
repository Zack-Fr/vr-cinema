<?php
require(__DIR__ . "/../models/Users.php");
require_once(__DIR__ . "/../config/connection.php");
require(__DIR__ . "/../services/MovieService.php");
require(__DIR__ . "/../services/ResponseService.php");

class AuthController {

public static function loginUser()
    {
    global $mysqli;

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
// $_SESSION['is_admin'] = $userRecord['is_admin'];
// successful login
$_SESSION['user_id'] = $user->toArray()['id'];
echo json_encode(['status'=>200,'user'=>$user->toArray()]); 
}
public static function registerUser(){
    global $mysqli;
    $input = json_decode(file_get_contents('php://input'), true);
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    echo json_encode($input['email']);
    echo json_encode($input['password']);
    echo json_encode($input['name']);
    exit;
}

try {
    // call create method
    $newId = User::create($mysqli, $input);

    //immediate Session login
    $_SESSION['user_id'] = $newId;
    
    http_response_code(201);
    echo json_encode(['status'=>201, 'user_id'=>$newId]);

} catch (Exception $e) {

    if (strpos($e->getMessage(), '1062') !== false) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
}
}
public static function logoutUser() {
    

    session_start();

// Clear server-side session data
$_SESSION = [];

// Get current cookie params
$params = session_get_cookie_params();

// Expire the session cookie correctly
setcookie(
  session_name(),    // typically PHPSESSID
  '',                // no value
  time() - 3600,     // in the past
  $params['path'],   // usually '/'
  $params['domain'], // your domain, or ''
  $params['secure'], // true if you’re on HTTPS
  $params['httponly'], // true to keep it HttpOnly
  // For PHP 7.3+ you can also add:
);

// Destroy the session
session_destroy();

// Respond
http_response_code(200);
echo json_encode([
'status'  => 'success',
'message' => 'Logged out successfully'
]);

}
public static function getUser(){
    global $mysqli;
    session_start();

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'Not authenticated']);
    exit;
}

if(!isset($_GET["id"])){

    $userId =(int) $_GET['id'];
    $user = User::find($mysqli, $userId)->toArray(); 
    echo $user;
    
}
// GET vs PUT handling
if ($_SERVER['REQUEST_METHOD']==='GET') {
    $userId =(int) $_GET['id'];
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
//update data
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
}
}