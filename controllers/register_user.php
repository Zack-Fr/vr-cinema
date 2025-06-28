
<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Users.php';

$input = json_decode(file_get_contents('php://input'), true);



if (empty($input['email']) || empty($input['password']) || empty($input['age'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

try {
    // call create method
    $newId = User::create($mysqli, $input);

    
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
