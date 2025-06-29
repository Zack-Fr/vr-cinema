<?php

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

