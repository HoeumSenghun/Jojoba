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
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = sanitize($_POST['name']);
    $short_description = sanitize($_POST['short_description']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = $_POST['category_id'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
    
    // Handle image (either URL or upload)
    $image = null;
    
    // If image URL is provided, use it
    if (!empty($image_url) && filter_var($image_url, FILTER_VALIDATE_URL)) {
        $image = $image_url;
    } 
    // Otherwise, handle file upload
    elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $filename;
        }
    }
    
    try {
        // If editing existing product
        if ($id) {
            // Get current product data
            $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $current_product = $stmt->fetch();
            
            // If no new image was uploaded, keep the existing one
            if (!$image && $current_product) {
                $image = $current_product['image'];
            }
            
            // Update product
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, short_description = ?, description = ?, price = ?, 
                    stock = ?, category_id = ?, featured = ?, image = ? 
                WHERE id = ?
            ");
            $stmt->execute([$name, $short_description, $description, $price, $stock, $category_id, $featured, $image, $id]);
            
            setFlashMessage('success', 'Product updated successfully.');
        } else {
            // Insert new product
            $stmt = $pdo->prepare("
                INSERT INTO products (name, short_description, description, price, stock, category_id, featured, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $short_description, $description, $price, $stock, $category_id, $featured, $image]);
            
            setFlashMessage('success', 'Product added successfully.');
        }
        
        redirect('/admin/products');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
        redirect('/admin/products');
    }
} else {
    // If not a POST request, redirect
    redirect('/admin/products');
}

