<?php
session_start();
require_once '../config/database.php';
require_once '../helpers/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    
    // Validate quantity
    if ($quantity <= 0) {
        $quantity = 1;
    }
    
    try {
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            setFlashMessage('error', 'Product not found.');
            redirect('/products');
            exit;
        }
        
        // Check if product is in stock
        if ($product['stock'] < $quantity) {
            setFlashMessage('error', 'Not enough stock available.');
            redirect('/product?id=' . $product_id);
            exit;
        }
        
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product is already in cart
        $product_exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                // Update quantity
                $item['quantity'] += $quantity;
                $product_exists = true;
                break;
            }
        }
        
        // If product is not in cart, add it
        if (!$product_exists) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }
        
        setFlashMessage('success', 'Product added to cart.');
        
        // Redirect back to the referring page or to the cart
        if (isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect('/cart');
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
        redirect('/products');
    }
} else {
    // If not a POST request, redirect
    redirect('/products');
}