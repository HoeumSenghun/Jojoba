<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
$pageTitle = 'Products';
require_once 'includes/header.php';

// Check if we're editing a product
$editing = false;
$product = [
    'id' => '',
    'name' => '',
    'short_description' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category_id' => '',
    'featured' => 0,
    'image' => ''
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $foundProduct = $stmt->fetch();
    
    if ($foundProduct) {
        $editing = true;
        $product = $foundProduct;
    }
}

// Get all categories for the dropdown
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Get all products for the list
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $editing ? 'Edit Product' : 'Products' ?></h1>
    <?php if (!$editing): ?>
        <button id="add-product-btn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i> Add Product
        </button>
    <?php endif; ?>
</div>

<!-- Product Form (hidden by default if not editing) -->
<div id="product-form" class="bg-white rounded-lg shadow-md p-6 mb-6 <?= $editing ? '' : 'hidden' ?>">
    <h2 class="text-xl font-semibold mb-4"><?= $editing ? 'Edit Product' : 'Add New Product' ?></h2>
    
    <form action="/admin/actions/save-product.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" id="name" name="name" value="<?= $product['name'] ?>" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category_id" name="category_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" id="price" name="price" value="<?= $product['price'] ?>" min="0" step="0.01" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>" min="0" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div class="md:col-span-2">
                <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                <input type="text" id="short_description" name="short_description" value="<?= $product['short_description'] ?>" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                <textarea id="description" name="description" rows="4" required
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"><?= $product['description'] ?></textarea>
            </div>
            
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                <?php if ($product['image'] && file_exists('../uploads/products/' . $product['image'])): ?>
                    <div class="mb-2">
                        <img src="/uploads/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="h-32 w-32 object-cover rounded-md">
                    </div>
                <?php elseif ($product['image'] && filter_var($product['image'], FILTER_VALIDATE_URL)): ?>
                    <div class="mb-2">
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="h-32 w-32 object-cover rounded-md">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" <?= $editing && !filter_var($product['image'], FILTER_VALIDATE_URL) ? '' : '' ?>
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
    
                <div class="mt-3">
                    <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">Or Image URL</label>
                    <input type="url" id="image_url" name="image_url" value="<?= filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : '' ?>" 
                           placeholder="https://example.com/image.jpg"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">Enter a URL if you want to use an external image instead of uploading</p>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="featured" name="featured" value="1" <?= $product['featured'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                <label for="featured" class="ml-2 block text-sm text-gray-700">Featured Product</label>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="/admin/products" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                <?= $editing ? 'Update Product' : 'Add Product' ?>
            </button>
        </div>
    </form>
</div>

<!-- Products List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($products as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" src="<?= getProductImage($item['image']) ?>" alt="<?= $item['name'] ?>">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $item['name'] ?></div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs"><?= $item['short_description'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $item['category_name'] ?? 'Uncategorized' ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= formatPrice($item['price']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($item['stock'] > 10): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <?= $item['stock'] ?> in stock
                                </span>
                            <?php elseif ($item['stock'] > 0): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <?= $item['stock'] ?> in stock
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of stock
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($item['featured']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                                    Featured
                                </span>
                            <?php else: ?>
                                <span class="text-sm text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/admin/products?id=<?= $item['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                            <a href="/admin/actions/delete-product.php?id=<?= $item['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (count($products) === 0): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Toggle product form visibility
    const addProductBtn = document.getElementById('add-product-btn');
    const productForm = document.getElementById('product-form');
    
    if (addProductBtn && productForm) {
        addProductBtn.addEventListener('click', () => {
            productForm.classList.toggle('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>

