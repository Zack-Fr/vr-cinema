<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
  // echo back the exact origin instead of '*'
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


// 2. Handle preflight and exit early
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
exit(0);
}

$path = $_SERVER['PATH_INFO'] ?? '';
if ($path) {
  // replace REQUEST_URI for the router
$_SERVER['REQUEST_URI'] = $path;
}

// 4. Hand off to your router
require __DIR__ . '/../routes/api.php';

