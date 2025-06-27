<?php

$uri = str_replace('/ProjectOneBackend/public/index.php', '', $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];


if ($uri === '/register' && $method === 'POST') {
    require_once '../controllers/register_user.php';//include this controller file
    $controller = new register_user();//create a new object to handles request/response logic
    $controller->register();//call register method, handles validation, DB save
    exit(); 
}