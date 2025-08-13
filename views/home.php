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
    
    // Use home-specific settings if available, otherwise fall back to site settings
    $homeTitle = !empty($siteSettings['home']['title']) ? $siteSettings['home']['title'] : $siteSettings['site_name'];
    $homeDescription = !empty($siteSettings['home']['meta_description']) ? $siteSettings['home']['meta_description'] : $siteSettings['site_description'];
    
    // Generate meta tags for homepage
    $meta = generateMetaTags(
        $homeTitle,
        $homeDescription,
        '/'
    );
    
    include __DIR__ . '/partials/meta-tags.php';
    
    // Include schema helper
    require_once __DIR__ . '/../app/helpers/schema.php';
    ?>
    
    <!-- JSON-LD Schema Markup for Homepage -->
    <?php
    // Generate Website Schema
    $websiteSchema = generateWebsiteSchema($siteSettings);
    outputJsonLdSchema($websiteSchema);
    
    // Generate Organization Schema
    $organizationSchema = generateOrganizationSchema($siteSettings);
    outputJsonLdSchema($organizationSchema);
    ?>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-2xl mx-auto px-5 py-8">
                <nav class="mb-8">
            <div class="flex space-x-6">
                <a href="/categories" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">Kategori</a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/admin" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">Admin</a>
                <?php endif; ?>
            </div>

        <h1 class="text-3xl font-bold text-gray-900 mt-8"><?php echo $siteSettings['site_name']?></h1>
        <span class="text-xl text-gray-900 mb-8"><?php echo $siteSettings['site_description']?></span>
        <div class="space-y-6">
        <?php foreach($posts as $p): ?>
            <article class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="text-xl font-semibold flex-1">
                        <a href="/post/<?=htmlspecialchars($p['slug'])?>" 
                           class="text-blue-600 hover:text-blue-800 hover:underline">
                            <?=htmlspecialchars($p['title'])?>
                        </a>
                    </h2>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <a href="/admin/posts/<?= $p['id'] ?>/edit" 
                           class="ml-3 inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                           title="Edit artikel">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="text-sm text-gray-500 flex items-center space-x-2">
                    <span><?=date('j F Y', strtotime($p['created_at']))?></span>
                    <?php if(!empty($p['category_name'])): ?>
                        <span>•</span>
                        <a href="/category/<?=htmlspecialchars($p['category_slug'])?>" 
                           class="text-blue-600 hover:text-blue-800 hover:underline">
                            <?=htmlspecialchars($p['category_name'])?>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
