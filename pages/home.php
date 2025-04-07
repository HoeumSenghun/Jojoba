<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Get featured products
$stmt = $pdo->prepare("SELECT * FROM products WHERE featured = 1 LIMIT 15");
$stmt->execute();
$featuredProducts = $stmt->fetchAll();

// Get categories
$stmt = $pdo->prepare("SELECT * FROM categories LIMIT 4");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="bg-primary rounded-lg py-12 px-4 md:px-8 mb-12">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-8 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold text-primary-800 mb-4">Natural Beauty with Jojoba</h1>
            <p class="text-lg text-gray-700 mb-6">Discover our premium range of natural beauty products made with organic Jojoba oil. Nourish your skin the way nature intended.</p>
            <div class="flex space-x-4">
                <a href="/products" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-md transition duration-300">Shop Now</a>
                <a href="/about" class="bg-white hover:bg-gray-100 text-primary-700 font-medium py-3 px-6 rounded-md border border-primary-600 transition duration-300">Learn More</a>
            </div>
        </div>
        <div class="md:w-1/2">
            <img src="../assets/images/image.jpg" alt="Jojoba Beauty Products" class="rounded-lg shadow-lg">
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Shop by Category</h2>
        <a href="/products" class="text-primary-600 hover:text-primary-700 font-medium">View All</a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($categories as $category): ?>
        <a href="/products?category=<?= $category['id'] ?>" class="group">
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 transform group-hover:scale-105">
                <div class="h-48 overflow-hidden">
                    <img src="<?= !empty($category['image']) && filter_var($category['image'], FILTER_VALIDATE_URL) ? $category['image'] : (!empty($category['image']) ? 'uploads/categories/' . $category['image'] : '/assets/images/category-placeholder.jpg') ?>" 
                         alt="<?= $category['name'] ?>" 
                         class="w-full h-full object-cover transition duration-300 group-hover:opacity-90">
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-gray-800"><?= $category['name'] ?></h3>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Featured Products</h2>
        <a href="/products" class="text-primary-600 hover:text-primary-700 font-medium">View All</a>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 hover:shadow-lg">
            <a href="/product?id=<?= $product['id'] ?>">
                <div class="h-48 sm:h-56 overflow-hidden">
                    <img src="<?= getProductImage($product['image']) ?>" 
                         alt="<?= $product['name'] ?>" 
                         class="w-full h-full object-cover transition duration-300 hover:scale-105">
                </div>
            </a>
            <div class="p-4">
                <a href="/product?id=<?= $product['id'] ?>">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2"><?= $product['name'] ?></h3>
                </a>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= $product['short_description'] ?></p>
                <div class="flex justify-between items-center">
                    <span class="text-primary-700 font-bold"><?= formatPrice($product['price']) ?>$</span>
                    <form action="/actions/add-to-cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="bg-primary-100 hover:bg-primary-200 text-primary-700 px-3 py-1 rounded-md transition duration-300">
                            <i class="fas fa-shopping-cart mr-1"></i> Add
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Benefits Section -->
<section class="mb-12 bg-gray-50 py-10 px-4 rounded-lg">
    <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">Why Choose Jojoba?</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-primary-500 text-4xl mb-4">
                <i class="fas fa-leaf"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">100% Natural</h3>
            <p class="text-gray-600">All our products are made with natural ingredients, free from harmful chemicals.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-primary-500 text-4xl mb-4">
                <i class="fas fa-heart"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Cruelty-Free</h3>
            <p class="text-gray-600">We never test on animals and are committed to ethical production methods.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-primary-500 text-4xl mb-4">
                <i class="fas fa-recycle"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Eco-Friendly</h3>
            <p class="text-gray-600">Our packaging is recyclable and we strive to minimize our environmental impact.</p>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="mb-12">
    <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">What Our Customers Say</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="text-yellow-400 flex">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <p class="text-gray-600 mb-4">"I've been using the Jojoba facial serum for a month now and my skin has never looked better. It's hydrating without being greasy and has really improved my complexion."</p>
            <div class="font-semibold">Sarah T.</div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="text-yellow-400 flex">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <p class="text-gray-600 mb-4">"The Jojoba hair oil is a game-changer! My hair feels so much softer and healthier since I started using it. Plus, it smells amazing!"</p>
            <div class="font-semibold">Michael R.</div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
                <div class="text-yellow-400 flex">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
            <p class="text-gray-600 mb-4">"I love that these products are all-natural and still work so effectively. The body butter is my favorite - it keeps my skin moisturized all day long."</p>
            <div class="font-semibold">Jennifer L.</div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="bg-primary-600 text-white py-10 px-4 rounded-lg">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-2xl font-bold mb-3">Join Our Newsletter</h2>
        <p class="mb-6">Subscribe to get special offers, free giveaways, and product launches.</p>
        
        <form action="/actions/subscribe.php" method="post" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
            <input type="email" name="email" placeholder="Your email address" required class="flex-grow px-4 py-2 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-300">
            <button type="submit" class="bg-white text-primary-700 hover:bg-gray-100 font-medium px-6 py-2 rounded-md transition duration-300">Subscribe</button>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

