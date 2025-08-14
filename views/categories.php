<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Semua Kategori - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="Daftar semua kategori di <?=htmlspecialchars($siteSettings['site_name'])?>">
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Semua Kategori</h1>
            <p class="text-xl text-gray-600">Jelajahi artikel berdasarkan kategori</p>
        </div>

        <?php if (empty($categories)): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada kategori</h3>
                <p class="text-gray-500">Kategori akan muncul setelah administrator menambahkannya.</p>
            </div>
        <?php else: ?>
            <div class="grid gap-6 md:grid-cols-2">
                <?php foreach($categories as $cat): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300">
                        <h2 class="text-xl font-semibold mb-3">
                            <a href="/category/<?=htmlspecialchars($cat['slug'])?>" 
                               class="text-gray-900 hover:text-blue-600 transition-colors">
                                <?=htmlspecialchars($cat['name'])?>
                            </a>
                        </h2>
                        <?php if (!empty($cat['description'])): ?>
                            <p class="text-gray-600 mb-4 leading-relaxed"><?=htmlspecialchars($cat['description'])?></p>
                        <?php endif ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <?=number_format($cat['post_count'])?> artikel
                            </span>
                            <a href="/category/<?=htmlspecialchars($cat['slug'])?>" 
                               class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                Lihat semua →
                            </a>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</body>
</html>
