<?php
function getSiteSettings() {
    $configPath = __DIR__ . '/../../config/site.json';
    if (file_exists($configPath)) {
        $content = file_get_contents($configPath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

function getReadingTime($content) {
    if (empty($content)) {
        return 0;
    }
    
    $wordCount = str_word_count(strip_tags($content));
    return max(1, ceil($wordCount / 200)); // 200 words per minute average
}

function formatReadingTime($minutes) {
    if ($minutes == 1) {
        return '1 menit baca';
    }
    return $minutes . ' menit baca';
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
