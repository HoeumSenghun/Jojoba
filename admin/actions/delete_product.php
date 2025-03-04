<?php
session_start();
require_once '../../config/database.php';
require_once '../../helpers/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You do not have permission to perform this action.');
    redirect('/login');
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get product image before deleting
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        // Delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete product image if it exists
        if ($product && $product['image'] && file_exists('../../uploads/products/' . $product['image'])) {
            unlink('../../uploads/products/' . $product['image']);
        }
        
        setFlashMessage('success', 'Product deleted successfully.');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
    }
} else {
    setFlashMessage('error', 'Invalid product ID.');
}

redirect('/admin/products');