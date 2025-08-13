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
    <title><?=htmlspecialchars($post['meta_title'] ?? $post['title'])?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="<?=htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? $siteSettings['site_description'])?>">
    
    <!-- Enhanced OpenGraph Meta Tags -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?=htmlspecialchars($post['meta_title'] ?? $post['title'])?>">
    <meta property="og:description" content="<?=htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? $siteSettings['site_description'])?>">
    <meta property="og:url" content="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/post/<?=htmlspecialchars($post['slug'])?>">
    <meta property="og:site_name" content="<?=htmlspecialchars($siteSettings['site_name'])?>">
    <?php if (!empty($post['featured_image'])): ?>
    <meta property="og:image" content="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?><?=htmlspecialchars($post['featured_image'])?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    <meta property="article:published_time" content="<?=date('c', strtotime($post['created_at']))?>">
    <meta property="article:modified_time" content="<?=date('c', strtotime($post['updated_at'] ?? $post['created_at']))?>">
    <?php if (!empty($post['category_name'])): ?>
    <meta property="article:section" content="<?=htmlspecialchars($post['category_name'])?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?=htmlspecialchars($post['meta_title'] ?? $post['title'])?>">
    <meta name="twitter:description" content="<?=htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? $siteSettings['site_description'])?>">
    <?php if (!empty($post['featured_image'])): ?>
    <meta name="twitter:image" content="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?><?=htmlspecialchars($post['featured_image'])?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/post/<?=htmlspecialchars($post['slug'])?>">
    
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <link href="/css/style.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    typography: {
                        DEFAULT: {
                            css: {
                                maxWidth: 'none',
                                color: '#374151',
                                lineHeight: '1.75',
                                fontSize: '1rem',
                                h1: {
                                    fontSize: '2.25em',
                                    fontWeight: '800',
                                    lineHeight: '1.2',
                                    marginTop: '0',
                                    marginBottom: '0.8889em',
                                },
                                h2: {
                                    fontSize: '1.5em',
                                    fontWeight: '700',
                                    lineHeight: '1.3333',
                                    marginTop: '2em',
                                    marginBottom: '1em',
                                },
                                h3: {
                                    fontSize: '1.25em',
                                    fontWeight: '600',
                                    lineHeight: '1.6',
                                    marginTop: '1.6em',
                                    marginBottom: '0.6em',
                                },
                                p: {
                                    marginTop: '1.25em',
                                    marginBottom: '1.25em',
                                },
                                a: {
                                    color: '#2563eb',
                                    textDecoration: 'underline',
                                    fontWeight: '500',
                                },
                                'a:hover': {
                                    color: '#1d4ed8',
                                },
                                strong: {
                                    color: '#111827',
                                    fontWeight: '600',
                                },
                                ul: {
                                    listStyleType: 'disc',
                                    marginTop: '1.25em',
                                    marginBottom: '1.25em',
                                    paddingLeft: '1.625em',
                                },
                                ol: {
                                    listStyleType: 'decimal',
                                    marginTop: '1.25em',
                                    marginBottom: '1.25em',
                                    paddingLeft: '1.625em',
                                },
                                li: {
                                    marginTop: '0.5em',
                                    marginBottom: '0.5em',
                                },
                                blockquote: {
                                    fontWeight: '500',
                                    fontStyle: 'italic',
                                    color: '#374151',
                                    borderLeftWidth: '0.25rem',
                                    borderLeftColor: '#d1d5db',
                                    quotes: '"\\201C""\\201D""\\2018""\\2019"',
                                    marginTop: '1.6em',
                                    marginBottom: '1.6em',
                                    paddingLeft: '1em',
                                },
                                code: {
                                    color: '#111827',
                                    backgroundColor: '#f3f4f6',
                                    paddingLeft: '0.25rem',
                                    paddingRight: '0.25rem',
                                    paddingTop: '0.125rem',
                                    paddingBottom: '0.125rem',
                                    borderRadius: '0.25rem',
                                    fontSize: '0.875em',
                                },
                                pre: {
                                    color: '#374151',
                                    backgroundColor: '#f9fafb',
                                    overflowX: 'auto',
                                    fontSize: '0.875em',
                                    lineHeight: '1.7142857',
                                    marginTop: '1.7142857em',
                                    marginBottom: '1.7142857em',
                                    borderRadius: '0.375rem',
                                    paddingTop: '0.8571429em',
                                    paddingRight: '1.1428571em',
                                    paddingBottom: '0.8571429em',
                                    paddingLeft: '1.1428571em',
                                },
                                img: {
                                    marginTop: '2em',
                                    marginBottom: '2em',
                                    borderRadius: '0.5rem',
                                },
                                table: {
                                    width: '100%',
                                    tableLayout: 'auto',
                                    textAlign: 'left',
                                    marginTop: '2em',
                                    marginBottom: '2em',
                                    fontSize: '0.875em',
                                    lineHeight: '1.7142857',
                                },
                                thead: {
                                    borderBottomWidth: '1px',
                                    borderBottomColor: '#d1d5db',
                                },
                                'thead th': {
                                    color: '#111827',
                                    fontWeight: '600',
                                    verticalAlign: 'bottom',
                                    paddingRight: '0.5714286em',
                                    paddingBottom: '0.5714286em',
                                    paddingLeft: '0.5714286em',
                                },
                                'tbody tr': {
                                    borderBottomWidth: '1px',
                                    borderBottomColor: '#e5e7eb',
                                },
                                'tbody td': {
                                    verticalAlign: 'baseline',
                                    paddingTop: '0.5714286em',
                                    paddingRight: '0.5714286em',
                                    paddingBottom: '0.5714286em',
                                    paddingLeft: '0.5714286em',
                                },
                            },
                        },
                    },
                },
            },
        }
    </script>
    
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
    <div class="max-w-2xl mx-auto px-5 py-8">
        <?php 
        // Check admin status once
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

        <article>
            <?php if(!empty($post['featured_image'])): ?>
                <div class="mb-6">
                    <img src="<?=htmlspecialchars($post['featured_image'])?>" 
                         alt="<?=htmlspecialchars($post['title'])?>"
                         class="w-full h-auto rounded-lg"
                         loading="eager"
                         decoding="async">
                </div>
            <?php endif; ?>

            <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">
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

            <div class="prose prose-lg max-w-none" style="
                max-width: none;
                color: #374151;
                line-height: 1.75;
                font-size: 1rem;
            ">
                <style>
                .prose h1 { font-size: 2.25em; font-weight: 800; line-height: 1.2; margin-top: 0; margin-bottom: 0.8889em; color: #111827; }
                .prose h2 { font-size: 1.5em; font-weight: 700; line-height: 1.3333; margin-top: 2em; margin-bottom: 1em; color: #111827; }
                .prose h3 { font-size: 1.25em; font-weight: 600; line-height: 1.6; margin-top: 1.6em; margin-bottom: 0.6em; color: #111827; }
                .prose p { margin-top: 1.25em; margin-bottom: 1.25em; }
                .prose a { color: #2563eb; text-decoration: underline; font-weight: 500; }
                .prose a:hover { color: #1d4ed8; }
                .prose strong { color: #111827; font-weight: 600; }
                .prose ul { list-style-type: disc; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
                .prose ol { list-style-type: decimal; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
                .prose li { margin-top: 0.5em; margin-bottom: 0.5em; }
                .prose blockquote { 
                    font-weight: 500; 
                    font-style: italic; 
                    color: #374151; 
                    border-left: 0.25rem solid #d1d5db; 
                    margin-top: 1.6em; 
                    margin-bottom: 1.6em; 
                    padding-left: 1em; 
                }
                .prose code { 
                    color: #111827; 
                    background-color: #f3f4f6; 
                    padding: 0.125rem 0.25rem; 
                    border-radius: 0.25rem; 
                    font-size: 0.875em; 
                }
                .prose pre { 
                    color: #374151; 
                    background-color: #f9fafb; 
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
                }
                .prose thead { border-bottom: 1px solid #d1d5db; }
                .prose thead th { 
                    color: #111827; 
                    font-weight: 600; 
                    vertical-align: bottom; 
                    padding: 0 0.5714286em 0.5714286em 0.5714286em; 
                }
                .prose tbody tr { border-bottom: 1px solid #e5e7eb; }
                .prose tbody td { 
                    vertical-align: baseline; 
                    padding: 0.5714286em; 
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

            <?php if(!empty($attachments)): ?>
                <div class="mt-12 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Unduh</h3>
                    <div class="space-y-2">
                        <a href="/download/pdf?slug=<?=urlencode($post['slug'])?>" 
                           class="download-link inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                            Artikel dalam format PDF
                        </a>
                        <a href="/download/docx?slug=<?=urlencode($post['slug'])?>" 
                           class="download-link inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors ml-2">
                            Artikel dalam format DOCX
                        </a>
                        <?php foreach($attachments as $f): ?>
                            <div>
                                <a href="/download/<?=htmlspecialchars($f['filename'])?>" 
                                   target="_blank"
                                   class="download-link inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors" style="word-break:break-word">
                                    <?=htmlspecialchars($f['filename'])?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
    </div>
</body>
</html>
