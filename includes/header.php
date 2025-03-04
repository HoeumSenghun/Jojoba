<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jojoba Shop - <?= $pageTitle ?? 'Natural Beauty Products' ?></title>
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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-primary-700">Jojoba Shop</a>
                
                <nav class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-600 hover:text-primary-600">Home</a>
                    <a href="/products" class="text-gray-600 hover:text-primary-600">Products</a>
                    <a href="/about" class="text-gray-600 hover:text-primary-600">About</a>
                    <a href="/contact" class="text-gray-600 hover:text-primary-600">Contact</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="/cart" class="text-gray-600 hover:text-primary-600 relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-primary-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                <?= count($_SESSION['cart']) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="relative group">
                            <button class="text-gray-600 hover:text-primary-600">
                                <i class="fas fa-user"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                <?php if (isAdmin()): ?>
                                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                <?php endif; ?>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <a href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="text-gray-600 hover:text-primary-600">Login</a>
                        <a href="/register" class="bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600">Register</a>
                    <?php endif; ?>
                    
                    <button id="mobile-menu-button" class="md:hidden text-gray-600">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobile-menu" class="md:hidden hidden mt-4">
                <nav class="flex flex-col space-y-2">
                    <a href="/" class="text-gray-600 hover:text-primary-600 py-2">Home</a>
                    <a href="/products" class="text-gray-600 hover:text-primary-600 py-2">Products</a>
                    <a href="/about" class="text-gray-600 hover:text-primary-600 py-2">About</a>
                    <a href="/contact" class="text-gray-600 hover:text-primary-600 py-2">Contact</a>
                </nav>
            </div>
        </div>
    </header>
    
    <?php
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="container mx-auto px-4 mt-4">
        <div class="<?= $flash['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> px-4 py-3 rounded relative border" role="alert">
            <span class="block sm:inline"><?= $flash['message'] ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <main class="flex-grow container mx-auto px-4 py-6">

