<?php

/**
 * Generate JSON-LD Schema markup for SEO
 */

function generateArticleSchema($post, $siteSettings) {
    // Get full URL
    $baseUrl = rtrim($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'], '/');
    $articleUrl = $baseUrl . '/post/' . $post['slug'];
    
    // Schema data
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post['title'],
        'description' => $post['meta_description'] ?? $post['excerpt'] ?? '',
        'url' => $articleUrl,
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $articleUrl
        ],
        'author' => [
            '@type' => 'Organization',
            'name' => $siteSettings['site_name'],
            'url' => $baseUrl
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteSettings['site_name'],
            'url' => $baseUrl
        ],
        'datePublished' => date('c', strtotime($post['created_at'])),
        'dateModified' => date('c', strtotime($post['updated_at'] ?? $post['created_at']))
    ];

    // Add image if exists
    if (!empty($post['featured_image'])) {
        $imageUrl = $baseUrl . $post['featured_image'];
        $schema['image'] = [
            '@type' => 'ImageObject',
            'url' => $imageUrl,
            'width' => 1200, // Default width
            'height' => 630  // Default height
        ];
    }

    // Add category/section if exists
    if (!empty($post['category_name'])) {
        $schema['articleSection'] = $post['category_name'];
    }

    // Add word count estimation and reading time
    if (!empty($post['content_md'])) {
        $wordCount = str_word_count(strip_tags($post['content_md']));
        $schema['wordCount'] = $wordCount;
        
        // Reading time estimation (average 200 words per minute)
        $readingTimeMinutes = max(1, ceil($wordCount / 200));
        $schema['timeRequired'] = 'PT' . $readingTimeMinutes . 'M';
    }

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function generateWebsiteSchema($siteSettings) {
    $baseUrl = rtrim($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'], '/');
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteSettings['site_name'],
        'description' => $siteSettings['site_description'],
        'url' => $baseUrl,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $baseUrl . '/search?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function generateOrganizationSchema($siteSettings) {
    $baseUrl = rtrim($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'], '/');
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteSettings['site_name'],
        'description' => $siteSettings['site_description'],
        'url' => $baseUrl
    ];

    // Add logo if exists
    if (!empty($siteSettings['site_favicon'])) {
        $schema['logo'] = [
            '@type' => 'ImageObject',
            'url' => $baseUrl . $siteSettings['site_favicon']
        ];
    }

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function generateBreadcrumbSchema($breadcrumbs, $siteSettings) {
    $baseUrl = rtrim($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'], '/');
    
    $items = [];
    foreach ($breadcrumbs as $index => $crumb) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $baseUrl . $crumb['url']
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function generateCategorySchema($category, $posts, $siteSettings) {
    $baseUrl = rtrim($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'], '/');
    $categoryUrl = $baseUrl . '/category/' . $category['slug'];
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $category['name'],
        'description' => $category['description'] ?? 'Artikel dalam kategori ' . $category['name'],
        'url' => $categoryUrl,
        'mainEntity' => [
            '@type' => 'ItemList',
            'name' => $category['name'],
            'numberOfItems' => count($posts)
        ]
    ];

    // Add list items
    $items = [];
    foreach ($posts as $index => $post) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => $baseUrl . '/post/' . $post['slug'],
            'name' => $post['title']
        ];
    }
    
    if (!empty($items)) {
        $schema['mainEntity']['itemListElement'] = $items;
    }

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function outputJsonLdSchema($schemaJson) {
    echo '<script type="application/ld+json">' . "\n";
    echo $schemaJson . "\n";
    echo '</script>' . "\n";
}
