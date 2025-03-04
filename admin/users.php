<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
$pageTitle = 'Users';
require_once 'includes/header.php';

// Check if we're editing a user
$editing = false;
$user = [
    'id' => '',
    'name' => '',
    'email' => '',
    'role' => 'customer',
    'status' => 'active'
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $foundUser = $stmt->fetch();
    
    if ($foundUser) {
        $editing = true;
        $user = $foundUser;
    }
}

// Get all users for the list
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY name");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $editing ? 'Edit User' : 'Users' ?></h1>
    <?php if (!$editing): ?>
        <button id="add-user-btn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i> Add User
        </button>
    <?php endif; ?>
</div>

<!-- User Form (hidden by default if not editing) -->
<div id="user-form" class="bg-white rounded-lg shadow-md p-6 mb-6 <?= $editing ? '' : 'hidden' ?>">
    <h2 class="text-xl font-semibold mb-4"><?= $editing ? 'Edit User' : 'Add New User' ?></h2>
    
    <form action="/admin/actions/save-user.php" method="post">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="<?= $user['name'] ?>" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= $editing ? 'New Password (leave blank to keep current)' : 'Password' ?>
                </label>
                <input type="password" id="password" name="password" <?= $editing ? '' : 'required' ?>
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="role

