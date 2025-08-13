<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title><?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="<?=htmlspecialchars($siteSettings['site_description'])?>">
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>
    <div class="max-w-2xl mx-auto px-5 py-8">
        <nav class="mb-8">
            <div class="flex space-x-6">
                <a href="/categories" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">Kategori</a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/admin" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">Admin</a>
                <?php endif; ?>
            </div>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Daftar Dokumen</h1>

        <div class="space-y-6">
        <?php foreach($posts as $p): ?>
            <article class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <h2 class="text-xl font-semibold mb-2">
                    <a href="/post/<?=htmlspecialchars($p['slug'])?>" 
                       class="text-blue-600 hover:text-blue-800 hover:underline">
                        <?=htmlspecialchars($p['title'])?>
                    </a>
                </h2>
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
