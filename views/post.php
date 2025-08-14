<?php 
// Check admin status once
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$isAdmin = !empty($_SESSION['is_admin']);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    require_once __DIR__ . '/../app/helpers/schema.php';
    $siteSettings = getSiteSettings();
    
    // Generate meta tags for post
    $meta = array(
        'title' => ($post['meta_title'] ?? $post['title']) . ' - ' . $siteSettings['site_name'],
        'description' => $post['meta_description'] ?? $post['excerpt'] ?? $siteSettings['site_description'],
        'canonical_url' => ($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) . '/post/' . $post['slug'],
        'og_type' => 'article',
        'og_image' => !empty($post['featured_image']) ? ($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) . $post['featured_image'] : null,
        'twitter_image' => !empty($post['featured_image']) ? ($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) . $post['featured_image'] : null,
        'robots' => 'index, follow'
    );
    
    include __DIR__ . '/partials/meta-tags.php';
    ?>
    
    <!-- Article-specific meta tags -->
    <meta property="article:published_time" content="<?=date('c', strtotime($post['created_at']))?>">
    <meta property="article:modified_time" content="<?=date('c', strtotime($post['updated_at'] ?? $post['created_at']))?>">
    <?php if (!empty($post['category_name'])): ?>
    <meta property="article:section" content="<?=htmlspecialchars($post['category_name'])?>">
    <?php endif; ?>
    <?php if (!empty($post['featured_image'])): ?>
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <!-- JSON-LD Schema Markup -->
    <?php
    // Generate Article Schema
    $articleSchema = generateArticleSchema($post, $siteSettings);
    outputJsonLdSchema($articleSchema);
    
    // Generate Breadcrumb Schema
    $breadcrumbs = [
        ['name' => 'Beranda', 'url' => '/']
    ];
    if (!empty($post['category_name'])) {
        $breadcrumbs[] = ['name' => $post['category_name'], 'url' => '/category/' . $post['category_slug']];
    }
    $breadcrumbs[] = ['name' => $post['title'], 'url' => '/post/' . $post['slug']];
    
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
                <?php if (!empty($post['category_name'])): ?>
                    <a href="/category/<?=htmlspecialchars($post['category_slug'])?>" 
                       class="hover:text-gray-900 transition-colors">
                        <?=htmlspecialchars($post['category_name'])?>
                    </a>
                    <span>→</span>
                <?php endif; ?>
                <span class="text-gray-900 font-medium">
                    <?=htmlspecialchars($post['title'])?>
                </span>
            </div>
        </nav>

        <article class="bg-white rounded-xl border border-gray-200 p-8">
            <?php if(!empty($post['featured_image'])): ?>
                <div class="mb-8">
                    <img src="<?=htmlspecialchars($post['featured_image'])?>" 
                         alt="<?=htmlspecialchars($post['title'])?>"
                         class="w-full h-auto rounded-lg shadow-sm"
                         loading="eager"
                         decoding="async">
                </div>
            <?php endif; ?>

            <h1 class="text-4xl font-bold text-gray-900 mb-6 leading-tight">
                <?=htmlspecialchars($post['title'])?>
            </h1>

            <?php if ($isAdmin): ?>
                <div class="mb-4">
                    <a href="/admin/posts/<?= $post['id'] ?>/edit" 
                       class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Artikel
                    </a>
                </div>
            <?php endif; ?>

            <div class="text-sm text-gray-500 mb-6 flex items-center space-x-2">
                <span><?=date('j F Y', strtotime($post['created_at']))?></span>
                <?php if(!empty($post['category_name'])): ?>
                    <span>•</span>
                    <a href="/category/<?=htmlspecialchars($post['category_slug'])?>" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        <?=htmlspecialchars($post['category_name'])?>
                    </a>
                <?php endif; ?>
                <?php if(!empty($post['content_md'])): ?>
                    <span>•</span>
                    <span><?= formatReadingTime(getReadingTime($post['content_md'])) ?></span>
                <?php endif; ?>
            </div>

            <?php if(!empty($post['excerpt'])): ?>
                <div class="text-lg text-gray-600 italic mb-6 font-medium">
                    <?=htmlspecialchars($post['excerpt'])?>
                </div>
            <?php endif; ?>

            <!-- Collapsible Attachments Section (after excerpt) -->
            <?php if(!empty($attachments)): ?>
                <div class="mb-6">
                    <button id="attachmentsToggle" 
                            class="flex items-center w-full px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg id="attachmentsIcon" class="w-5 h-5 text-blue-600 mr-3 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <span class="font-medium text-blue-800">📎 Unduh Lampiran</span>
                        <span class="ml-4 min-w-fit text-right text-blue-600 text-sm">🕳</span>
                    </button>
                    
                    <div id="attachmentsContent" class="hidden mt-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">📥 Unduh File:</h4>
                        <div class="space-y-2">
                            <!-- PDF/DOCX Export -->
                            <div class="flex flex-wrap gap-2">
                                <a href="/download/pdf?slug=<?=urlencode($post['slug'])?>" 
                                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                    </svg>
                                    Artikel (PDF)
                                </a>
                                <a href="/download/docx?slug=<?=urlencode($post['slug'])?>" 
                                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                    </svg>
                                    Artikel (DOCX)
                                </a>
                            </div>
                            
                            <!-- Additional Attachments -->
                            <?php if(count($attachments) > 0): ?>
                                <div class="border-t border-gray-300 pt-3 mt-3">
                                    <p class="text-xs text-gray-600 mb-2">File Lampiran:</p>
                                    <div class="space-y-1">
                                        <?php foreach($attachments as $f): ?>
                                            <a href="/download/<?=htmlspecialchars($f['filename'])?>" 
                                               target="_blank"
                                               class="flex items-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <span class="truncate"><?=htmlspecialchars($f['filename'])?></span>
                                                <span class="ml-auto text-xs text-gray-500">↗</span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="prose prose-lg max-w-none" style="
                max-width: none;
                color: var(--text-primary);
                line-height: 1.75;
                font-size: 1rem;
            ">
                <style>
                .prose h1 { font-size: 2.25em; font-weight: 800; line-height: 1.2; margin-top: 0; margin-bottom: 0.8889em; color: var(--text-primary); }
                .prose h2 { font-size: 1.5em; font-weight: 700; line-height: 1.3333; margin-top: 2em; margin-bottom: 1em; color: var(--text-primary); }
                .prose h3 { font-size: 1.25em; font-weight: 600; line-height: 1.6; margin-top: 1.6em; margin-bottom: 0.6em; color: var(--text-primary); }
                .prose p { margin-top: 1.25em; margin-bottom: 1.25em; color: var(--text-primary); }
                .prose a { color: var(--text-accent); text-decoration: underline; font-weight: 500; }
                .prose a:hover { color: var(--text-accent); }
                .prose strong { color: var(--text-primary); font-weight: 600; }
                .prose ul { list-style-type: disc; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
                .prose ol { list-style-type: decimal; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
                .prose li { margin-top: 0.5em; margin-bottom: 0.5em; color: var(--text-primary); }
                .prose blockquote { 
                    font-weight: 500; 
                    font-style: italic; 
                    color: var(--text-secondary); 
                    border-left: 0.25rem solid var(--border-secondary); 
                    margin-top: 1.6em; 
                    margin-bottom: 1.6em; 
                    padding-left: 1em; 
                }
                .prose code { 
                    color: var(--text-primary); 
                    background-color: var(--bg-tertiary); 
                    padding: 0.125rem 0.25rem; 
                    border-radius: 0.25rem; 
                    font-size: 0.875em; 
                }
                .prose pre { 
                    color: var(--text-primary); 
                    background-color: var(--bg-tertiary); 
                    overflow-x: auto; 
                    font-size: 0.875em; 
                    line-height: 1.7142857; 
                    margin-top: 1.7142857em; 
                    margin-bottom: 1.7142857em; 
                    border-radius: 0.375rem; 
                    padding: 0.8571429em 1.1428571em; 
                }
                .prose img { 
                    margin-top: 2em; 
                    margin-bottom: 2em; 
                    border-radius: 0.5rem; 
                    max-width: 100%; 
                    height: auto; 
                }
                .prose table { 
                    width: 100%; 
                    table-layout: auto; 
                    text-align: left; 
                    margin-top: 2em; 
                    margin-bottom: 2em; 
                    font-size: 0.875em; 
                    line-height: 1.7142857; 
                    color: var(--text-primary);
                }
                .prose thead { border-bottom: 1px solid var(--border-secondary); }
                .prose thead th { 
                    color: var(--text-primary); 
                    font-weight: 600; 
                    vertical-align: bottom; 
                    padding: 0 0.5714286em 0.5714286em 0.5714286em; 
                }
                .prose tbody tr { border-bottom: 1px solid var(--border-tertiary); }
                .prose tbody td { 
                    vertical-align: baseline; 
                    padding: 0.5714286em;
                    color: var(--text-primary); 
                }
                </style>
                <?php 
                    // Load Parsedown
                    if (!class_exists('Parsedown')) {
                        require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';
                    }
                    $Parsedown = new Parsedown(); 
                    $Parsedown->setSafeMode(true); 
                    echo $Parsedown->text($post['content_md']); 
                ?>
            </div>
        </article>
    </div>
    
    <script>
    // Attachments toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('attachmentsToggle');
        const content = document.getElementById('attachmentsContent');
        const icon = document.getElementById('attachmentsIcon');
        
        if (toggle && content && icon) {
            toggle.addEventListener('click', function() {
                const isHidden = content.classList.contains('hidden');
                
                if (isHidden) {
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(90deg)';
                    toggle.querySelector('span:last-child').textContent = '👁';
                } else {
                    content.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                    toggle.querySelector('span:last-child').textContent = '🕳';
                }
            });
        }
    });
    </script>
</body>
</html>
