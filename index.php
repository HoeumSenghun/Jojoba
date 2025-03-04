<?php
session_start();
require_once 'config/database.php';
require_once 'helpers/functions.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$base_path = '/jojoba-shop'; // Change this to match your local setup

// Remove base path from request
$request = str_replace($base_path, '', $request);

// Default route
if ($request == '/' || $request == '') {
    require 'pages/home.php';
    exit;
}

// Routes
$routes = [
    '/products' => 'pages/products.php',
    '/product' => 'pages/product-detail.php',
    '/cart' => 'pages/cart.php',
    '/checkout' => 'pages/checkout.php',
    '/login' => 'pages/login.php',
    '/register' => 'pages/register.php',
    '/logout' => 'actions/logout.php',
    '/admin' => 'admin/index.php',
    '/admin/products' => 'admin/products.php',
    '/admin/categories' => 'admin/categories.php',
    '/admin/orders' => 'admin/orders.php',
    '/admin/users' => 'admin/users.php',
];

// Check if route exists
foreach ($routes as $route => $file) {
    if (strpos($request, $route) === 0) {
        require $file;
        exit;
    }
}

// 404 page
require 'pages/404.php';

