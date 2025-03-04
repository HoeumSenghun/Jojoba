<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
$pageTitle = 'Categories';
require_once 'includes/header.php';

// Check if we're editing a category
$editing = false;
$category = [
    'id' => '',
    'name' => '',
    'description' => '',
    'image' => ''
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $foundCategory = $stmt->fetch();
    
    if ($foundCategory) {
        $editing = true;
        $category = $foundCategory;
    }
}

// Get all categories for the list
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $editing ? 'Edit Category' : 'Categories' ?></h1>
    <?php if (!$editing): ?>
        <button id="add-category-btn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i> Add Category
        </button>
    <?php endif; ?>
</div>

<!-- Category Form (hidden by default if not editing) -->
<div id="category-form" class="bg-white rounded-lg shadow-md p-6 mb-6 <?= $editing ? '' : 'hidden' ?>">
    <h2 class="text-xl font-semibold mb-4"><?= $editing ? 'Edit Category' : 'Add New Category' ?></h2>
    
    <form action="/admin/actions/save-category.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $category['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" id="name" name="name" value="<?= $category['name'] ?>" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
            </div>
            
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"><?= $category['description'] ?></textarea>
            </div>
            
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Category Image</label>
                <?php if ($category['image'] && file_exists('../uploads/categories/' . $category['image'])): ?>
                    <div class="mb-2">
                        <img src="/uploads/categories/<?= $category['image'] ?>" alt="<?= $category['name'] ?>" class="h-32 w-32 object-cover rounded-md">
                    </div>
                <?php elseif ($category['image'] && filter_var($category['image'], FILTER_VALIDATE_URL)): ?>
                    <div class="mb-2">
                        <img src="<?= $category['image'] ?>" alt="<?= $category['name'] ?>" class="h-32 w-32 object-cover rounded-md">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" <?= $editing && !filter_var($category['image'], FILTER_VALIDATE_URL) ? '' : '' ?>
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
    
                <div class="mt-3">
                    <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">Or Image URL</label>
                    <input type="url" id="image_url" name="image_url" value="<?= filter_var($category['image'], FILTER_VALIDATE_URL) ? $category['image'] : '' ?>" 
                           placeholder="https://example.com/image.jpg"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">Enter a URL if you want to use an external image instead of uploading</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="/admin/categories" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                <?= $editing ? 'Update Category' : 'Add Category' ?>
            </button>
        </div>
    </form>
</div>

<!-- Categories List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($categories as $item): ?>
                    <?php
                    // Get product count for this category
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                    $stmt->execute([$item['id']]);
                    $productCount = $stmt->fetchColumn();
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" 
                                         src="<?= !empty($item['image']) && file_exists('../uploads/categories/' . $item['image']) ? '/uploads/categories/' . $item['image'] : '/assets/images/category-placeholder.jpg' ?>" 
                                         alt="<?= $item['name'] ?>">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $item['name'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 truncate max-w-xs"><?= $item['description'] ?? '-' ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $productCount ?> products</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/admin/categories?id=<?= $item['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                            <a href="/admin/actions/delete-category.php?id=<?= $item['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this category? All associated products will be uncategorized.')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (count($categories) === 0): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Toggle category form visibility
    const addCategoryBtn = document.getElementById('add-category-btn');
    const categoryForm = document.getElementById('category-form');
    
    if (addCategoryBtn && categoryForm) {
        addCategoryBtn.addEventListener('click', () => {
            categoryForm.classList.toggle('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>

