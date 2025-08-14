<?php
// Simple Footer partial
require_once __DIR__ . '/../../app/helpers/site.php';
$siteSettings = getSiteSettings();
$currentYear = date('Y');
?>

<footer class="bg-white border-t border-gray-200 mt-12 pb-12">
    <div class="max-w-2xl mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <!-- Copyright -->
            <div class="text-sm text-gray-500 mb-3 md:mb-0">
                © <?php echo $currentYear; ?> <?php echo htmlspecialchars($siteSettings['site_name']); ?>. 
                <span class="hidden md:inline">Semua hak dilindungi.</span>
            </div>

            <!-- Footer Links -->
            <div class="flex items-center space-x-4 text-sm">
                <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">
                    Kebijakan Privasi
                </a>
                <span class="text-gray-300">•</span>
                <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">
                    Syarat Penggunaan
                </a>
                <span class="text-gray-300">•</span>
                <div class="flex items-center text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Made with care
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<div id="backToTop" 
     style="position: fixed; 
            bottom: 24px; 
            left: 24px; 
            width: 50px; 
            height: 50px; 
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; 
            border-radius: 50%; 
            cursor: pointer; 
            display: none; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); 
            z-index: 9999;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, opacity 0.3s ease;
            border: none;
            outline: none;
            font-family: system-ui, -apple-system, sans-serif;
            opacity: 0;"
     title="Kembali ke atas"
     onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 8px 25px rgba(59, 130, 246, 0.5)'; this.style.background='linear-gradient(135deg, #2563eb, #1d4ed8)';"
     onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(59, 130, 246, 0.4)'; this.style.background='linear-gradient(135deg, #3b82f6, #2563eb)';"
     onclick="window.scrollTo({top: 0, behavior: 'smooth'}); this.style.transform='scale(0.92)'; setTimeout(() => this.style.transform='scale(1)', 150);">
    
    <!-- SVG Arrow with perfect centering -->
    <svg style="width: 20px; 
                height: 20px; 
                display: block; 
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                stroke-width: 2.5;
                filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));" 
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</div>

<script>
// Fixed back to top functionality - no transform conflicts
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        var backToTop = document.getElementById('backToTop');
        
        if (backToTop) {
            var isVisible = false;
            
            function showButton() {
                if (!isVisible) {
                    isVisible = true;
                    backToTop.style.display = 'flex';
                    // Use timeout to ensure display is set before opacity
                    setTimeout(function() {
                        backToTop.style.opacity = '1';
                    }, 10);
                }
            }
            
            function hideButton() {
                if (isVisible) {
                    isVisible = false;
                    backToTop.style.opacity = '0';
                    setTimeout(function() {
                        if (!isVisible) {
                            backToTop.style.display = 'none';
                        }
                    }, 300);
                }
            }
            
            function toggleButton() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > 300) {
                    showButton();
                } else {
                    hideButton();
                }
            }
            
            // Throttled scroll listener for better performance
            var ticking = false;
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(function() {
                        toggleButton();
                        ticking = false;
                    });
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick, { passive: true });
            
            // Initial check
            toggleButton();
        }
    });
})();
</script>
