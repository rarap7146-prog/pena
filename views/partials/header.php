<?php
// Header navigation partial
require_once __DIR__ . '/../../app/helpers/site.php';
$siteSettings = getSiteSettings();
?>

<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-4xl mx-auto px-4 py-3">
        <!-- Desktop Layout -->
        <div class="hidden md:flex items-center justify-between">
            <!-- Logo/Site Name -->
            <div class="flex items-center space-x-4">
                <a href="/" class="text-xl font-bold text-gray-900 hover:text-gray-700">
                    <?php echo htmlspecialchars($siteSettings['site_name']); ?>
                </a>
            </div>

            <!-- Search Box -->
            <div class="flex-1 max-w-md mx-6">
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

            <!-- Navigation Links -->
            <nav class="flex items-center space-x-4">
                <a href="/categories" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                    Kategori
                </a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/admin" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                        Admin
                    </a>
                <?php endif; ?>
            </nav>
        </div>
        
        <!-- Mobile Layout -->
        <div class="md:hidden">
            <!-- Top Row: Logo and Menu Button -->
            <div class="flex items-center justify-between mb-3">
                <a href="/" class="text-lg font-bold text-gray-900">
                    <?php echo htmlspecialchars($siteSettings['site_name']); ?>
                </a>
                <button id="mobileMenuToggle" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search Box -->
            <form method="GET" action="/search" class="relative mb-3" id="mobileSearchForm">
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
            
            <!-- Mobile Navigation Menu -->
            <nav id="mobileMenu" class="hidden space-y-2">
                <a href="/categories" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md text-sm font-medium transition-colors">
                    Kategori
                </a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/admin" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md text-sm font-medium transition-colors">
                        Admin
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<script>
// Mobile menu functionality
(function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
})();

// Search suggestions functionality
(function() {
    function setupSearchSuggestions(inputId, suggestionsId, formId) {
        const searchInput = document.getElementById(inputId);
        const searchSuggestions = document.getElementById(suggestionsId);
        const searchForm = document.getElementById(formId);
        let timeoutId;

        if (searchInput && searchSuggestions) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(timeoutId);
                
                if (query.length < 2) {
                    searchSuggestions.classList.add('hidden');
                    return;
                }
                
                timeoutId = setTimeout(() => {
                    fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(suggestions => {
                            if (suggestions.length > 0) {
                                searchSuggestions.innerHTML = suggestions
                                    .map(suggestion => 
                                        `<div class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-b-0" onclick="selectSuggestion('${suggestion.replace(/'/g, "\\'")}', '${inputId}', '${formId}')">
                                            ${suggestion}
                                        </div>`
                                    ).join('');
                                searchSuggestions.classList.remove('hidden');
                            } else {
                                searchSuggestions.classList.add('hidden');
                            }
                        })
                        .catch(() => {
                            searchSuggestions.classList.add('hidden');
                        });
                }, 300);
            });
            
            searchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    searchSuggestions.classList.add('hidden');
                }, 150);
            });
            
            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && searchSuggestions.innerHTML.trim()) {
                    searchSuggestions.classList.remove('hidden');
                }
            });
        }
    }

    // Setup for desktop search
    setupSearchSuggestions('searchInput', 'searchSuggestions', 'searchForm');
    
    // Setup for mobile search
    setupSearchSuggestions('mobileSearchInput', 'mobileSearchSuggestions', 'mobileSearchForm');

    window.selectSuggestion = function(suggestion, inputId, formId) {
        const searchInput = document.getElementById(inputId);
        const searchForm = document.getElementById(formId);
        
        if (searchInput && searchForm) {
            searchInput.value = suggestion;
            document.getElementById('searchSuggestions')?.classList.add('hidden');
            document.getElementById('mobileSearchSuggestions')?.classList.add('hidden');
            searchForm.submit();
        }
    };
})();
</script>
