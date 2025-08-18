<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    require_once __DIR__ . '/../app/helpers/schema.php';
    $siteSettings = getSiteSettings();
    
    // Generate meta tags for category
    $meta = array(
        'title' => $category['name'] . ' - ' . $siteSettings['site_name'],
        'description' => $category['description'] ?? 'Artikel dalam kategori ' . $category['name'],
        'canonical_url' => ($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) . '/category/' . $category['slug'],
        'og_type' => 'website',
        'robots' => 'index, follow'
    );
    
    include __DIR__ . '/partials/meta-tags.php';
    ?>
    
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
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="/" class="hover:text-gray-900 transition-colors">Beranda</a>
                <span>→</span>
                <a href="/categories" class="hover:text-gray-900 transition-colors">Kategori</a>
                <span>→</span>
                <span class="text-gray-900 font-medium"><?= htmlspecialchars($category['name']) ?></span>
            </div>
        </nav>

        <!-- Category Header -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="text-xl text-gray-600 leading-relaxed"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
            <div class="mt-4 text-sm text-gray-500">
                <?= count($posts) ?> artikel dalam kategori ini
            </div>
        </div>
        <?php 
        // Check admin status
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $isAdmin = !empty($_SESSION['is_admin']);
        ?>
        
        <?php if (!empty($posts)): ?>
            <div class="grid gap-6">
                <?php foreach ($posts as $post): ?>
                    <article class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300">
                        <div class="flex justify-between items-start mb-3">
                            <h2 class="text-xl font-semibold flex-1 leading-tight">
                                <a href="/post/<?= htmlspecialchars($post['slug']) ?>" 
                                   class="text-gray-900 hover:text-blue-600 transition-colors">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>
                            <?php if ($isAdmin): ?>
                                <a href="/admin/posts/<?= $post['id'] ?>/edit" 
                                   class="ml-3 inline-flex items-center px-3 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full hover:bg-gray-200 hover:text-gray-700 transition-all"
                                   title="Edit artikel">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-3 space-x-3">
                            <time datetime="<?= $post['created_at'] ?>">
                                <?= date('j F Y', strtotime($post['created_at'])) ?>
                            </time>
                        </div>
                        
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="text-gray-600 leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($pagination) && $pagination['total'] > 1): ?>
            <nav class="flex justify-center mt-12" aria-label="Category pagination">
                <div class="bg-white px-3 py-2 rounded-md shadow-sm">
                    <ul class="inline-flex items-center space-x-2 text-sm">
                        <?php if ($pagination['hasPrev']): ?>
                            <li>
                                <a href="<?= htmlspecialchars($pagination['prevUrl']) ?>" class="px-3 py-1.5 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition" rel="prev">&laquo; Prev</a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $total = $pagination['total'];
                        $current = $pagination['current'];
                        $pages = [];
                        if ($total <= 9) {
                            for ($i = 1; $i <= $total; $i++) $pages[] = $i;
                        } else {
                            $pages[] = 1;
                            $left = max(2, $current - 2);
                            $right = min($total - 1, $current + 2);
                            if ($left > 2) $pages[] = '...';
                            for ($i = $left; $i <= $right; $i++) $pages[] = $i;
                            if ($right < $total - 1) $pages[] = '...';
                            $pages[] = $total;
                        }
                        foreach ($pages as $p):
                            if ($p === '...'):
                        ?>
                            <li class="px-2 text-gray-400 select-none">&hellip;</li>
                        <?php else: ?>
                            <?php $isCurrent = $p == $current; ?>
                            <li>
                                <?php
                                    $pageUrl = $p == 1 ? htmlspecialchars($pagination['canonicalUrl']) : htmlspecialchars(rtrim($pagination['canonicalUrl'], '/')) . '/page/' . $p;
                                ?>
                                <a href="<?= $pageUrl ?>" class="px-3 py-1.5 border <?= $isCurrent ? 'bg-blue-500 text-white font-semibold border-blue-500' : 'bg-white text-gray-700 border-gray-200 hover:bg-blue-50 hover:text-blue-600' ?> rounded-md transition" <?= $isCurrent ? 'aria-current="page"' : '' ?>>
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endif; endforeach; ?>

                        <?php if ($pagination['hasNext']): ?>
                            <li>
                                <a href="<?= htmlspecialchars($pagination['nextUrl']) ?>" class="px-3 py-1.5 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition" rel="next">Next &raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Belum ada artikel</h3>
                <p class="text-gray-600 mb-8">Belum ada artikel dalam kategori "<?= htmlspecialchars($category['name']) ?>".</p>
                <?php if ($isAdmin): ?>
                    <a href="/admin/posts/create" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tulis Artikel Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
