</main>
    
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Jojoba Shop</h3>
                    <p class="text-gray-300">Your trusted source for natural beauty products made with premium Jojoba oil.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="/products" class="text-gray-300 hover:text-white">Products</a></li>
                        <li><a href="/about" class="text-gray-300 hover:text-white">About Us</a></li>
                        <li><a href="/contact" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="/shipping" class="text-gray-300 hover:text-white">Shipping Policy</a></li>
                        <li><a href="/returns" class="text-gray-300 hover:text-white">Returns & Refunds</a></li>
                        <li><a href="/faq" class="text-gray-300 hover:text-white">FAQ</a></li>
                        <li><a href="/privacy" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-pinterest"></i></a>
                    </div>
                    <div class="mt-4">
                        <p class="text-gray-300">Email: info@jojobashop.com</p>
                        <p class="text-gray-300">Phone: +1 (555) 123-4567</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-300">
                <p>&copy; <?= date('Y') ?> Jojoba Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>

