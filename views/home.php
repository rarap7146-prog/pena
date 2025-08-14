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
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($siteSettings['site_name']); ?></h1>
            <p class="text-xl text-gray-600 mb-8"><?php echo htmlspecialchars($siteSettings['site_description']); ?></p>
        </div>
        
        <!-- Posts Grid -->
        <div class="grid gap-6">
        <?php foreach($posts as $p): ?>
            <article class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300">
                <div class="flex justify-between items-start mb-3">
                    <h2 class="text-xl font-semibold flex-1 leading-tight">
                        <a href="/post/<?=htmlspecialchars($p['slug'])?>" 
                           class="text-gray-900 hover:text-blue-600 transition-colors">
                            <?=htmlspecialchars($p['title'])?>
                        </a>
                    </h2>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <a href="/admin/posts/<?= $p['id'] ?>/edit" 
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
                    <time datetime="<?= $p['created_at'] ?>">
                        <?=date('j F Y', strtotime($p['created_at']))?>
                    </time>
                    <?php if(!empty($p['category_name'])): ?>
                        <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                        <a href="/category/<?=htmlspecialchars($p['category_slug'])?>" 
                           class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors">
                            <?=htmlspecialchars($p['category_name'])?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if(!empty($p['excerpt'])): ?>
                    <p class="text-gray-600 leading-relaxed"><?=htmlspecialchars($p['excerpt'])?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
