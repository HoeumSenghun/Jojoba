<?php
// Helper functions

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format price
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Get product image URL
function getProductImage($image) {
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    } elseif ($image && file_exists('uploads/products/' . $image)) {
        return 'uploads/products/' . $image;
    }
    return 'assets/images/image.jpg';
}

// Get category image URL
function getCategoryImage($image) {
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    } elseif ($image && file_exists('uploads/categories/' . $image)) {
        return 'uploads/categories/' . $image;
    }
    return 'assets/images/image.jpg';
}

