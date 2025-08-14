<?php
// Header navigation partial
require_once __DIR__ . '/../../app/helpers/site.php';
$siteSettings = getSiteSettings();

// Detect if we're on home page
$isHomePage = ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' || $_SERVER['REQUEST_URI'] === '');
?>

<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-2xl mx-auto px-4 py-3">
        <!-- Desktop Layout -->
        <div class="hidden md:flex items-center justify-between">
            <!-- Logo/Site Name -->
            <div class="flex items-center space-x-4">
                <a href="/" class="text-xl font-bold text-gray-900 hover:text-gray-700">
                    <?php echo htmlspecialchars($siteSettings['site_name']); ?>
                </a>
            </div>

            <!-- Search Box -->
            <?php if (!$isHomePage): ?>
            <div class="flex-1 max-w-sm mx-6">
                <form method="GET" action="/search" class="relative" id="searchForm">
                    <input type="text" 
                           name="q" 
                           id="searchInput"
                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                           placeholder="Cari artikel..." 
                           class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Search Suggestions Dropdown -->
                    <div id="searchSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                        <!-- Suggestions will be populated via JavaScript -->
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Navigation -->
            <nav class="flex items-center space-x-6">
                <a href="/" class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                    Home
                </a>
                <a href="/categories" class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                    Kategori
                </a>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                    <a href="/admin" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm font-medium hover:bg-blue-700">
                        Admin
                    </a>
                    <a href="/admin/logout" class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                        Logout
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="md:hidden text-gray-700 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Layout -->
        <div class="md:hidden">
            <!-- Mobile Header -->
            <div class="flex items-center justify-between">
                <a href="/" class="text-xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($siteSettings['site_name']); ?>
                </a>
                <button id="mobileMenuToggle" class="text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Search Box (Show only when not on home page) -->
            <?php if (!$isHomePage): ?>
            <div class="mt-4">
                <form method="GET" action="/search" class="relative" id="mobileSearchForm">
                    <input type="text" 
                           name="q" 
                           id="mobileSearchInput"
                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                           placeholder="Cari artikel..." 
                           class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Mobile Search Suggestions -->
                    <div id="mobileSearchSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                        <!-- Suggestions will be populated via JavaScript -->
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Mobile Menu (Collapsible Navigation) -->
            <div id="mobileMenu" class="hidden mt-4">
                <!-- Navigation Links -->
                <nav class="space-y-2 border-t border-gray-200 pt-4">
                    <a href="/" class="block py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Home
                    </a>
                    <a href="/categories" class="block py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Kategori
                    </a>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <a href="/admin" class="block py-2 bg-blue-600 text-white px-3 rounded-md font-medium hover:bg-blue-700">
                            Admin
                        </a>
                        <a href="/admin/logout" class="block py-2 text-gray-700 hover:text-gray-900 font-medium">
                            Logout
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</header>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Search suggestions functionality (conditional based on page)
    const searchInputs = <?php echo $isHomePage ? "['mobileSearchInput']" : "['searchInput', 'mobileSearchInput']"; ?>;
    
    searchInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const suggestionsId = inputId === 'searchInput' ? 'searchSuggestions' : 'mobileSearchSuggestions';
        const suggestionsContainer = document.getElementById(suggestionsId);
        
        if (!suggestionsContainer) return;
        
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionsContainer.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.suggestions.length > 0) {
                            suggestionsContainer.innerHTML = data.suggestions.map(suggestion => 
                                `<div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectSuggestion('${suggestion.title}', '${inputId}')">${suggestion.title}</div>`
                            ).join('');
                            suggestionsContainer.classList.remove('hidden');
                        } else {
                            suggestionsContainer.classList.add('hidden');
                        }
                    })
                    .catch(() => {
                        suggestionsContainer.classList.add('hidden');
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.classList.add('hidden');
            }
        });
    });
});

function selectSuggestion(title, inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = title;
        input.form.submit();
    }
}
</script>
