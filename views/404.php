<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    // Start session first before any output
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    
    // Generate meta tags for 404 page
    $meta = generateMetaTags(
        'Halaman Tidak Ditemukan - ' . $siteSettings['site_name'],
        'Halaman yang Anda cari tidak ditemukan. Kembali ke beranda atau gunakan pencarian untuk menemukan dokumen yang Anda butuhkan.',
        '/404'
    );
    
    include __DIR__ . '/partials/meta-tags.php';
    ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Floating 404 Illustration -->
        <div class="mb-12 float-animation">
            <div class="relative">
                <!-- Large 404 Numbers -->
                <div class="text-9xl md:text-[12rem] font-bold gradient-text mb-4 leading-none">
                    404
                </div>
                
                <!-- Decorative Elements -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-blue-100 rounded-full opacity-60"></div>
                <div class="absolute top-1/2 -left-8 w-8 h-8 bg-purple-100 rounded-full opacity-40"></div>
                <div class="absolute -bottom-2 right-1/4 w-12 h-12 bg-indigo-100 rounded-full opacity-50"></div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Oops! Halaman Tidak Ditemukan
            </h1>
            <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada. 
                Jangan khawatir, mari kita bantu Anda menemukan yang Anda butuhkan.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
            <a href="/" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>
            
            <a href="/categories" 
               class="inline-flex items-center px-6 py-3 bg-white text-gray-700 rounded-lg font-medium border border-gray-300 hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Jelajahi Kategori
            </a>
        </div>

        <!-- Search Box -->
        <div class="max-w-md mx-auto mb-8">
            <div class="relative">
                <input type="text" 
                       id="searchInput404"
                       placeholder="Cari dokumen, template, atau contoh..."
                       class="w-full px-4 py-3 pl-12 pr-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Popular Categories/Links -->
        <div class="border-t border-gray-200 pt-8">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Popular Categories -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Populer</h3>
                    <?php 
                    // Get top categories (count only published posts)
                    try {
                        $pdo = require __DIR__ . '/../app/db.php';
                        $categoriesStmt = $pdo->prepare("
                            SELECT c.name, c.slug, COUNT(p.id) as post_count 
                            FROM categories c 
                            LEFT JOIN posts p ON c.id = p.category_id
                                AND (p.status = 'published' 
                                     OR (p.status = 'scheduled' AND p.scheduled_at <= NOW()))
                            GROUP BY c.id 
                            HAVING post_count > 0
                            ORDER BY post_count DESC 
                            LIMIT 6
                        ");
                        $categoriesStmt->execute();
                        $categories = $categoriesStmt->fetchAll();
                    } catch(Exception $e) {
                        $categories = [];
                    }
                    ?>
                    
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($categories)): ?>
                            <?php foreach($categories as $category): ?>
                                <a href="/category/<?=htmlspecialchars($category['slug'])?>" 
                                   class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-800 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors">
                                    <?=htmlspecialchars($category['name'])?>
                                    <span class="ml-2 text-xs text-gray-600">(<?=$category['post_count']?>)</span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="/categories" class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-800 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors">
                                Lihat Semua Kategori
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen Terbaru</h3>
                    <?php 
                    // Get recent posts (only published and ready scheduled)
                    try {
                        $recentPostsStmt = $pdo->prepare("
                            SELECT p.title, p.slug, c.name as category_name 
                            FROM posts p 
                            LEFT JOIN categories c ON p.category_id = c.id
                            WHERE (p.status = 'published' 
                                   OR (p.status = 'scheduled' AND p.scheduled_at <= NOW()))
                            ORDER BY p.created_at DESC 
                            LIMIT 4
                        ");
                        $recentPostsStmt->execute();
                        $recentPosts = $recentPostsStmt->fetchAll();
                    } catch(Exception $e) {
                        $recentPosts = [];
                    }
                    ?>
                    
                    <div class="space-y-3">
                        <?php if (!empty($recentPosts)): ?>
                            <?php foreach($recentPosts as $post): ?>
                                <a href="/post/<?=htmlspecialchars($post['slug'])?>" 
                                   class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                    <div class="font-medium text-gray-900 text-sm mb-1">
                                        <?=htmlspecialchars($post['title'])?>
                                    </div>
                                    <?php if ($post['category_name']): ?>
                                        <div class="text-xs text-blue-600">
                                            <?=htmlspecialchars($post['category_name'])?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">Belum ada dokumen tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-12 text-center">
            <div class="bg-blue-50 rounded-lg p-6 mx-auto max-w-md">
                <div class="flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 class="font-semibold text-blue-900">Perlu Bantuan?</h4>
                </div>
                <p class="text-sm text-blue-800 mb-4">
                    Jika Anda yakin halaman ini seharusnya ada, mungkin ada link yang rusak.
                </p>
                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                    <a href="mailto:admin@araska.id?subject=Broken Link Report&body=URL yang rusak: <?=urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?>" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Laporkan Link Rusak
                    </a>
                    <button onclick="history.back()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                        </svg>
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for search functionality -->
    <script>
        document.getElementById('searchInput404').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = '/search?q=' + encodeURIComponent(query);
                }
            }
        });

        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on scroll/load
            const elements = document.querySelectorAll('h1, p, a, .float-animation');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <?php 
    // Include analytics if available
    if (file_exists(__DIR__ . '/../app/helpers/analytics.php')) {
        require_once __DIR__ . '/../app/helpers/analytics.php';
        renderAnalytics();
    }
    ?>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
