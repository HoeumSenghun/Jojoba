<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

// Get counts for dashboard
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Get recent orders
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");
$stmt->execute();
$recentOrders = $stmt->fetchAll();

// Get top selling products
$stmt = $pdo->prepare("
    SELECT p.*, SUM(oi.quantity) as total_sold 
    FROM products p 
    JOIN order_items oi ON p.id = oi.product_id 
    GROUP BY p.id 
    ORDER BY total_sold DESC 
    LIMIT 5
");
$stmt->execute();
$topProducts = $stmt->fetchAll();
?>

<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                <i class="fas fa-box text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-500 text-sm">Products</h2>
                <p class="text-2xl font-semibold"><?= $productCount ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-tags text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-500 text-sm">Categories</h2>
                <p class="text-2xl font-semibold"><?= $categoryCount ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-500 text-sm">Users</h2>
                <p class="text-2xl font-semibold"><?= $userCount ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-gray-500 text-sm">Orders</h2>
                <p class="text-2xl font-semibold"><?= $orderCount ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Recent Orders</h2>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($recentOrders) > 0): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="/admin/orders?id=<?= $order['id'] ?>" class="text-primary-600 hover:text-primary-800">#<?= $order['id'] ?></a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap"><?= $order['customer_name'] ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap"><?= formatPrice($order['total_amount']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($order['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'processing':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'completed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'cancelled':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>
                                        ">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="/admin/orders" class="text-primary-600 hover:text-primary-800 text-sm font-medium">View All Orders</a>
            </div>
        </div>
    </div>
    
    <!-- Top Selling Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Top Selling Products</h2>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($topProducts) > 0): ?>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img class="h-10 w-10 rounded-md object-cover" src="<?= getProductImage($product['image']) ?>" alt="<?= $product['name'] ?>">
                                            </div>
                                            <div class="ml-4">
                                                <a href="/admin/products?id=<?= $product['id'] ?>" class="text-sm font-medium text-gray-900 hover:text-primary-600"><?= $product['name'] ?></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap"><?= formatPrice($product['price']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap"><?= $product['total_sold'] ?> units</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if ($product['stock'] > 10): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= $product['stock'] ?> in stock
                                            </span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <?= $product['stock'] ?> in stock
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Out of stock
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No products found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="/admin/products" class="text-primary-600 hover:text-primary-800 text-sm font-medium">View All Products</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

