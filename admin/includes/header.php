<?php
// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You do not have permission to access the admin area.');
    redirect('/login');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jojoba Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f9f0',
                            100: '#e6f2dc',
                            200: '#cce5bc',
                            300: '#aad394',
                            400: '#85bd6d',
                            500: '#5a9d45',
                            600: '#4a8339',
                            700: '#3d692f',
                            800: '#345429',
                            900: '#2d4625',
                            950: '#162712',
                        },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white h-screen sticky top-0 overflow-y-auto hidden md:block">
        <div class="p-4 border-b border-gray-700">
            <h1 class="text-xl font-bold">Jojoba Shop</h1>
            <p class="text-sm text-gray-400">Admin Dashboard</p>
        </div>
        
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="/admin" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/admin/products" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-box mr-2"></i> Products
                    </a>
                </li>
                <li>
                    <a href="/admin/categories" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-tags mr-2"></i> Categories
                    </a>
                </li>
                <li>
                    <a href="/admin/orders" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-shopping-cart mr-2"></i> Orders
                    </a>
                </li>
                <li>
                    <a href="/admin/users" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-users mr-2"></i> Users
                    </a>
                </li>
                <li class="border-t border-gray-700 mt-4 pt-4">
                    <a href="/" class="block py-2 px-4 rounded hover:bg-gray-700">
                        <i class="fas fa-store mr-2"></i> View Store
                    </a>
                </li>
                <li>
                    <a href="/logout" class="block py-2 px-4 rounded hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <button id="sidebar-toggle" class="md:hidden text-gray-600">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="flex items-center space-x-1 focus:outline-none">
                        <img src="/assets/images/avatar-placeholder.jpg" alt="Admin" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline-block"><?= $_SESSION['user_name'] ?></span>
                        <i class="fas fa-chevron-  $_SESSION['user_name'] ?></span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                        <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                        <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Mobile Sidebar (hidden by default) -->
        <div id="mobile-sidebar" class="fixed inset-0 z-40 md:hidden hidden">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white overflow-y-auto">
                <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                    <h1 class="text-xl font-bold">Jojoba Shop</h1>
                    <button id="close-sidebar" class="text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <nav class="p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="/admin" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'bg-gray-700' : '' ?>">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/admin/products" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'bg-gray-700' : '' ?>">
                                <i class="fas fa-box mr-2"></i> Products
                            </a>
                        </li>
                        <li>
                            <a href="/admin/categories" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'bg-gray-700' : '' ?>">
                                <i class="fas fa-tags mr-2"></i> Categories
                            </a>
                        </li>
                        <li>
                            <a href="/admin/orders" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'bg-gray-700' : '' ?>">
                                <i class="fas fa-shopping-cart mr-2"></i> Orders
                            </a>
                        </li>
                        <li>
                            <a href="/admin/users" class="block py-2 px-4 rounded hover:bg-gray-700 <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'bg-gray-700' : '' ?>">
                                <i class="fas fa-users mr-2"></i> Users
                            </a>
                        </li>
                        <li class="border-t border-gray-700 mt-4 pt-4">
                            <a href="/" class="block py-2 px-4 rounded hover:bg-gray-700">
                                <i class="fas fa-store mr-2"></i> View Store
                            </a>
                        </li>
                        <li>
                            <a href="/logout" class="block py-2 px-4 rounded hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php
        $flash = getFlashMessage();
        if ($flash): 
        ?>
        <div class="p-4">
            <div class="<?= $flash['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> px-4 py-3 rounded relative border" role="alert">
                <span class="block sm:inline"><?= $flash['message'] ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Page Content -->
        <main class="flex-1 p-6">

