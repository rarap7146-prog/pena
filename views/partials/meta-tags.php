<?php
// Enhanced meta tags for SEO + Analytics
// Usage: include this in <head> section
// Example: $meta = generateMetaTags($post['meta_title'], $post['meta_description'], "/post/{$post['slug']}", $post['featured_image']);

if (!isset($meta)) {
    $meta = generateMetaTags();
}

// Load analytics helper
require_once __DIR__ . '/../../app/helpers/analytics.php';
?>

<!-- Basic Meta Tags -->
<title><?= htmlspecialchars($meta['title']) ?></title>
<meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
<link rel="canonical" href="<?= htmlspecialchars($meta['canonical']) ?>">

<!-- Google Search Console Verification -->
<?= renderGoogleSearchConsoleVerification() ?>

<!-- Open Graph (Facebook, LinkedIn, etc.) -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($meta['og_title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta['og_description']) ?>">
<meta property="og:image" content="<?= htmlspecialchars($meta['og_image']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($meta['og_url']) ?>">
<meta property="og:site_name" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($meta['twitter_title']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($meta['twitter_description']) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($meta['twitter_image']) ?>">

<!-- Additional SEO Meta Tags -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="robots" content="index, follow">
<meta name="language" content="id-ID">
<meta name="author" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">

<!-- Favicon -->
<link rel="icon" href="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">
<link rel="apple-touch-icon" href="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">

<!-- Google Analytics 4 -->
<?= renderGA4Script() ?>

<!-- Performance Monitoring -->
<?= renderPerformanceMonitoring() ?>
