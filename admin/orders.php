<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
$pageTitle = 'Orders';
require_once 'includes/header.php';

// Check if viewing a specific order
$viewingOrder = false;
$order = null;
$orderItems = [];

if (isset($_GET['id'])) {
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $order = $stmt->fetch();
    
    if ($order) {
        $viewingOrder = true;
        
        // Get order items
        $stmt = $pdo->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $orderItems = $stmt->fetchAll();
    }
} else {
    // Get all orders for the list
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll();
}
?>

<?php if ($viewingOrder && $order): ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Order #<?= $order['id'] ?></h1>
        <a href="/admin/orders" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Order Details</h2>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Order Date</p>
                        <p class="font-medium"><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="font-medium"><?= ucfirst($order['payment_method']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Status</p>
                        <p>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $order['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>
                            ">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <h3 class="text-md font-semibold mb-3">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-md object-cover" src="<?= getProductImage($item['product_image']) ?>" alt="<?= $item['product_name'] ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= $item['product_name'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-500">
                                        <?= formatPrice($item['price']) ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-500">
                                        <?= $item['quantity'] ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <?= formatPrice($item['price'] * $item['quantity']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-500">Subtotal</td>
                                <td class="px-4 py-3 text-right text-sm font-medium"><?= formatPrice($order['subtotal']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-500">Shipping</td>
                                <td class="px-4 py-3 text-right text-sm font-medium"><?= formatPrice($order['shipping_cost']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-500">Tax</td>
                                <td class="px-4 py-3 text-right text-sm font-medium"><?= formatPrice($order['tax']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900"><?= formatPrice($order['total_amount']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Customer and Shipping Info -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
                <p class="mb-1"><strong>Name:</strong> <?= $order['customer_name'] ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= $order['customer_email'] ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?= $order['phone'] ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
                <p class="mb-1"><?= $order['address_line1'] ?></p>
                <?php if (!empty($order['address_line2'])): ?>
                    <p class="mb-1"><?= $order['address_line2'] ?></p>
                <?php endif; ?>
                <p class="mb-1"><?= $order['city'] ?>, <?= $order['state'] ?> <?= $order['postal_code'] ?></p>
                <p class="mb-1"><?= $order['country'] ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Update Order Status</h2>
                <form action="/admin/actions/update-order.php" method="post">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                        <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select id="payment_status" name="payment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md">
                        Update Order
                    </button>
                </form>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Orders</h1>
    </div>
    
    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($orders) && count($orders) > 0): ?>
                        <?php foreach ($orders as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?= $item['id'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $item['customer_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= date('M d, Y', strtotime($item['created_at'])) ?></div>
                                    <div class="text-sm text-gray-500"><?= date('g:i A', strtotime($item['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= formatPrice($item['total_amount']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch ($item['status']) {
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
                                        <?= ucfirst($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $item['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>
                                    ">
                                        <?= ucfirst($item['payment_status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/admin/orders?id=<?= $item['id'] ?>" class="text-primary-600 hover:text-primary-900">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

