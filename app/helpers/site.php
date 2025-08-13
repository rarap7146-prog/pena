<?php
function getSiteSettings() {
    $configFile = __DIR__ . '/../../config/site.json';
    $defaults = [
        'site_name' => 'Araska.id',
        'site_description' => 'Dokumen dan informasi terkini',
        'site_favicon' => '/favicon.ico'
    ];
    
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true) ?? [];
        return array_merge($defaults, $config);
    }
    
    return $defaults;
}

function getCanonicalUrl($path = '') {
    $config = require __DIR__ . '/../../config/app.php';
    $baseUrl = $config['base_url'] ?? 'https://araska.id';
    return $baseUrl . $path;
}

function generateMetaTags($title = '', $description = '', $path = '', $image = '') {
    $siteSettings = getSiteSettings();
    $canonical = getCanonicalUrl($path);
    
    $title = $title ?: $siteSettings['site_name'];
    $description = $description ?: $siteSettings['site_description'];
    $image = $image ?: '/favicon.ico';
    
    if (!str_starts_with($image, 'http')) {
        $image = getCanonicalUrl($image);
    }
    
    return [
        'title' => $title,
        'description' => $description,
        'canonical' => $canonical,
        'og_title' => $title,
        'og_description' => $description,
        'og_image' => $image,
        'og_url' => $canonical,
        'twitter_title' => $title,
        'twitter_description' => $description,
        'twitter_image' => $image
    ];
}
?>
