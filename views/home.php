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
    
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-3"><?php echo htmlspecialchars($siteSettings['home']['hero_title'] ?? 'Template & Contoh Dokumen'); ?></h1>
            <p class="text-lg text-gray-600 mb-6"><?php echo htmlspecialchars($siteSettings['home']['hero_subtitle'] ?? 'Temukan dan unduh beragam template dokumen berkualitas tinggi untuk pelajar dan profesional'); ?></p>
            
            <!-- Featured Search -->
            <div class="max-w-md mx-auto mb-6">
                <form method="GET" action="/search" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="<?php echo htmlspecialchars($siteSettings['home']['search_placeholder'] ?? 'Cari template, contoh CV, makalah...'); ?>" 
                           class="w-full px-6 py-3 pl-12 pr-6 text-base border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm transition-all"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>
            
            <!-- Dynamic Quick Category Access -->
            <?php 
            // Get top 3 categories with most posts
            $topCategories = [];
            try {
                $pdo = require __DIR__ . '/../app/db.php';
                $topCategoriesStmt = $pdo->prepare("
                    SELECT c.*, COUNT(p.id) as post_count 
                    FROM categories c 
                    LEFT JOIN posts p ON c.id = p.category_id
                    GROUP BY c.id 
                    HAVING post_count > 0
                    ORDER BY post_count DESC 
                    LIMIT 3
                ");
                $topCategoriesStmt->execute();
                $topCategories = $topCategoriesStmt->fetchAll();
            } catch(Exception $e) {
                error_log("Home category error: " . $e->getMessage());
            }
            
            $categoryStyles = [
                'bg-blue-100 text-blue-800 hover:bg-blue-200',
                'bg-green-100 text-green-800 hover:bg-green-200', 
                'bg-purple-100 text-purple-800 hover:bg-purple-200'
            ];
            ?>
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <?php if (!empty($topCategories)): ?>
                    <?php foreach($topCategories as $index => $category): 
                        $style = $categoryStyles[$index] ?? 'bg-gray-100 text-gray-800 hover:bg-gray-200';
                    ?>
                        <a href="/category/<?=htmlspecialchars($category['slug'])?>" 
                           class="inline-flex items-center px-3 py-2 <?=$style?> rounded-full text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <?=htmlspecialchars($category['name'])?> (<?=$category['post_count']?>)
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="/categories" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Semua Kategori
                </a>
            </div>
        </div>

        <!-- Featured Documents Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Dokumen Terpopuler
                </h2>                
            </div>
        </div>
        
        <!-- Posts Grid -->
        <div class="grid gap-6">
        <?php foreach($posts as $p): ?>
            <article class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300 group">
                <div class="flex justify-between items-start mb-3">
                    <h2 class="text-xl font-semibold flex-1 leading-tight">
                        <a href="/post/<?=htmlspecialchars($p['slug'])?>" 
                           class="text-gray-900 hover:text-blue-600 transition-colors group-hover:text-blue-600">
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
                
                <?php if(!empty($p['excerpt'])): ?>
                    <p class="text-gray-600 leading-relaxed mb-4"><?=htmlspecialchars($p['excerpt'])?></p>
                <?php endif; ?>
                
                <!-- Document Stats & Meta -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-500">
                        <!-- Date -->
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <time datetime="<?= $p['created_at'] ?>">
                                <?=date('j M Y', strtotime($p['created_at']))?>
                            </time>
                        </div>
                    </div>
                    
                    <!-- Dynamic Document Type Badge -->
                    <?php if (!empty($p['category_name'])): ?>
                        <div class="flex items-center">
                            <div class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <?=htmlspecialchars($p['category_name'])?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
