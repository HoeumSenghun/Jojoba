<?php
session_start();
require_once '../config/database.php';
require_once '../helpers/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    try {
        // Get user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            setFlashMessage('success', 'Login successful. Welcome back, ' . $user['name'] . '!');
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                redirect('/admin');
            } else {
                redirect('/');
            }
        } else {
            setFlashMessage('error', 'Invalid email or password.');
            redirect('/login');
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
        redirect('/login');
    }
} else {
    // If not a POST request, redirect
    redirect('/login');
}