</main>
    </div>
    
    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const closeSidebar = document.getElementById('close-sidebar');
        
        if (sidebarToggle && mobileSidebar && closeSidebar) {
            sidebarToggle.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
            });
            
            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('hidden');
            });
        }
        
        // User dropdown toggle
        const userDropdownButton = document.querySelector('button.flex.items-center');
        const userDropdown = document.querySelector('button.flex.items-center + div');
        
        if (userDropdownButton && userDropdown) {
            userDropdownButton.addEventListener('click', () => {
                userDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!userDropdownButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>

