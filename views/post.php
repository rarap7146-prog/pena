<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title><?=htmlspecialchars($post['meta_title'] ?? $post['title'])?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="<?=htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? $siteSettings['site_description'])?>">
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-2xl mx-auto px-5 py-8">
        <nav class="mb-8">
            <a href="/" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">← Beranda</a>
        </nav>

        <article>
            <?php if(!empty($post['featured_image'])): ?>
                <div class="mb-6">
                    <img src="/uploads/featured-image/<?=htmlspecialchars($post['featured_image'])?>" 
                         alt="<?=htmlspecialchars($post['title'])?>"
                         class="w-full h-auto rounded-lg">
                </div>
            <?php endif; ?>

            <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">
                <?=htmlspecialchars($post['title'])?>
            </h1>

            <div class="text-sm text-gray-500 mb-6 flex items-center space-x-2">
                <span><?=date('j F Y', strtotime($post['created_at']))?></span>
                <?php if(!empty($post['category_name'])): ?>
                    <span>•</span>
                    <a href="/category/<?=htmlspecialchars($post['category_slug'])?>" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        <?=htmlspecialchars($post['category_name'])?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if(!empty($post['excerpt'])): ?>
                <div class="text-lg text-gray-600 italic mb-6 font-medium">
                    <?=htmlspecialchars($post['excerpt'])?>
                </div>
            <?php endif; ?>

            <div class="prose prose-lg max-w-none">
                <?php 
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
                                   class="download-link inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
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
