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
    $description = sanitize($_POST['description']);
    $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
    
    // Handle image (either URL or upload)
    $image = null;
    
    // If image URL is provided, use it
    if (!empty($image_url) && filter_var($image_url, FILTER_VALIDATE_URL)) {
        $image = $image_url;
    } 
    // Otherwise, handle file upload
    elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/categories/';
        
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
        // If editing existing category
        if ($id) {
            // Get current category data
            $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $current_category = $stmt->fetch();
            
            // If no new image was uploaded, keep the existing one
            if (!$image && $current_category) {
                $image = $current_category['image'];
            }
            
            // Update category
            $stmt = $pdo->prepare("
                UPDATE categories 
                SET name = ?, description = ?, image = ? 
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $image, $id]);
            
            setFlashMessage('success', 'Category updated successfully.');
        } else {
            // Insert new category
            $stmt = $pdo->prepare("
                INSERT INTO categories (name, description, image) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $description, $image]);
            
            setFlashMessage('success', 'Category added successfully.');
        }
        
        redirect('/admin/categories');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
        redirect('/admin/categories');
    }
} else {
    // If not a POST request, redirect
    redirect('/admin/categories');
}

