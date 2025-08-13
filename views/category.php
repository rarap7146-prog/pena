<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    require_once __DIR__ . '/../app/helpers/schema.php';
    $siteSettings = getSiteSettings();
    ?>
    <title><?= htmlspecialchars($category['name']) ?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="<?= htmlspecialchars($category['description'] ?? 'Artikel dalam kategori ' . $category['name']) ?>">
    
    <!-- Enhanced OpenGraph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($category['name']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($category['description'] ?? 'Artikel dalam kategori ' . $category['name']) ?>">
    <meta property="og:url" content="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/category/<?= htmlspecialchars($category['slug']) ?>">
    <meta property="og:site_name" content="<?=htmlspecialchars($siteSettings['site_name'])?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars($category['name']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($category['description'] ?? 'Artikel dalam kategori ' . $category['name']) ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/category/<?= htmlspecialchars($category['slug']) ?>">
    
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <link href="/css/style.css" rel="stylesheet">
    
    <!-- JSON-LD Schema Markup -->
    <?php
    // Generate Category Schema
    $categorySchema = generateCategorySchema($category, $posts, $siteSettings);
    outputJsonLdSchema($categorySchema);
    
    // Generate Breadcrumb Schema
    $breadcrumbs = [
        ['name' => 'Beranda', 'url' => '/'],
        ['name' => 'Kategori', 'url' => '/categories'],
        ['name' => $category['name'], 'url' => '/category/' . $category['slug']]
    ];
    
    $breadcrumbSchema = generateBreadcrumbSchema($breadcrumbs, $siteSettings);
    outputJsonLdSchema($breadcrumbSchema);
    ?>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-2xl mx-auto px-5 py-8">
        <?php 
        // Check admin status
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $isAdmin = !empty($_SESSION['is_admin']);
        ?>
        
        <nav class="mb-8">
            <div class="flex justify-between items-center">
                <a href="/" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">← Beranda</a>
                <?php if ($isAdmin): ?>
                    <a href="/admin" 
                       class="text-xs text-gray-500 hover:text-gray-700 transition-colors">
                        Admin Panel
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($category['name']) ?></h1>
                    <?php if (!empty($category['description'])): ?>
                        <p class="text-lg text-gray-600"><?= htmlspecialchars($category['description']) ?></p>
                    <?php endif ?>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="ml-6">
                        <a href="/admin/categories/<?= $category['id'] ?>/edit" 
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Kategori
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($posts)): ?>
            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-6">
                <span><?= count($posts) ?> artikel</span>
                <?php if (!empty($posts[0]['created_at'])): ?>
                    <span>•</span>
                    <span>Terakhir diperbarui: <?= date('j F Y', strtotime($posts[0]['created_at'])) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada artikel</h3>
                <p class="text-gray-500 mb-6">Belum ada artikel dalam kategori "<?= htmlspecialchars($category['name']) ?>".</p>
                <?php if ($isAdmin): ?>
                    <a href="/admin/posts/create" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tulis Artikel Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($posts as $post): ?>
                    <article class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-xl font-semibold flex-1">
                                <a href="/post/<?= htmlspecialchars($post['slug']) ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>
                            <?php if ($isAdmin): ?>
                                <a href="/admin/posts/<?= $post['id'] ?>/edit" 
                                   class="ml-3 inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                                   title="Edit artikel">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-gray-500 mb-3">
                            <?= date('j F Y', strtotime($post['created_at'])) ?>
                        </div>
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="text-gray-600">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</body>
</html>
