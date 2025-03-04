<?php
session_start();
require_once '../helpers/functions.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to home page
setFlashMessage('success', 'You have been logged out successfully.');
redirect('/');