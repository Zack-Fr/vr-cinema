<?php
require_once __DIR__ . '/../config/connection.php';

$scriptName = str_replace('\\','/', $_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);  
$basePath   = dirname($scriptName);                              

if (strpos($requestUri, $basePath) === 0) {
    $uri = substr($requestUri, strlen($basePath));
} else {
    $uri = $requestUri;
}
$uri = $uri === '' ? '/' : $uri;

$method = $_SERVER['REQUEST_METHOD'];

// Route dispatch
switch ("$method $uri") {

    // ─── Authentication ────────────────────────────────────────────────────────
    case 'POST /register':
        require_once __DIR__ . '/../controllers/register_user.php';
        break;
    case 'POST /login':
        require_once __DIR__ . '/../controllers/login_user.php';
        break;
    case 'POST /logout':
        require_once __DIR__ . '/../controllers/logout_user.php';
        break;

    // ─── User Profile ─────────────────────────────────────────────────────────
    case 'GET /profile':
        require_once __DIR__ . '/../controllers/user_profile.php';
        break;

    // ─── Movies & Showtimes ───────────────────────────────────────────────────
    case 'GET /movies':
        require_once __DIR__ . '/../controllers/get_movies.php';
        break;
    case 'GET /showtimes':
        require_once __DIR__ . '/../controllers/get_showtimes.php';
        break;

    // ─── Seating & Booking ────────────────────────────────────────────────────
    case 'GET  /seat-layout':
        require_once __DIR__ . '/../controllers/get_seat_layout.php';
        break;
    case 'POST /check-seats':
        require_once __DIR__ . '/../controllers/check_seat_availability.php';
        break;
    case 'POST /book':
        require_once __DIR__ . '/../controllers/book_seats.php';
        break;

    // ─── Discounts & Coupons ──────────────────────────────────────────────────
    case 'POST /apply-discount':
        require_once __DIR__ . '/../controllers/apply_discount.php';
        break;

    // ─── Social / Group Features ──────────────────────────────────────────────
    case 'POST /invite':
        require_once __DIR__ . '/../controllers/invite_friend.php';
        break;
    case 'POST /split-payment':
        require_once __DIR__ . '/../controllers/split_payment.php';
        break;

    // ─── Automation & Extras ──────────────────────────────────────────────────
    case 'POST /schedule-auto-booking':
        require_once __DIR__ . '/../controllers/schedule_auto_booking.php';
        break;
    case 'POST /preorder-snacks':
        require_once __DIR__ . '/../controllers/pre_order_snacks.php';
        break;

    // ─── Admin Panel ─────────────────────────────────────────────────────────
    case 'POST /admin/add-film':
        require_once __DIR__ . '/../controllers/admin/add_film.php';
        break;
    case 'POST /admin/import-showtimes':
        require_once __DIR__ . '/../controllers/admin/import_showtimes.php';
        break;
    case 'POST /admin/configure-seat-layout':
        require_once __DIR__ . '/../controllers/admin/configure_seat_layout.php';
        break;
    case 'POST /admin/adjust-pricing':
        require_once __DIR__ . '/../controllers/admin/adjust_pricing.php';
        break;

    // ─── Fallback ─────────────────────────────────────────────────────────────
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'method' => $method,
            'uri'    => $uri,
            'requestUri' =>$requestUri
            
        ]);
        
        break;
}
