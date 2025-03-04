<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You do not have permission to perform this action.');
    redirect('/login');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];
    
    try {
        // Update order
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = ?, payment_status = ? 
            WHERE id = ?
        ");
        $stmt->execute([$status, $payment_status, $order_id]);
        
        setFlashMessage('success', 'Order updated successfully.');
        redirect('/admin/orders?id=' . $order_id);
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
        redirect('/admin/orders?id=' . $order_id);
    }
} else {
    // If not a POST request, redirect
    redirect('/admin/orders');
}