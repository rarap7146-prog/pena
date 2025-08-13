<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title><?= htmlspecialchars($category['name']) ?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="<?= htmlspecialchars($category['description'] ?? 'Artikel dalam kategori ' . $category['name']) ?>">
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-2xl mx-auto px-5 py-8">
        <nav class="mb-8">
            <a href="/" class="text-gray-600 hover:text-gray-800 text-sm transition-colors">← Beranda</a>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="text-lg text-gray-600"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif ?>
        </div>

        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada artikel</h3>
                <p class="text-gray-500">Belum ada artikel dalam kategori ini.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($posts as $post): ?>
                    <article class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="/post/<?= htmlspecialchars($post['slug']) ?>" 
                               class="text-blue-600 hover:text-blue-800 hover:underline">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h2>
                        <div class="text-sm text-gray-500 mb-3">
                            <?= date('j F Y', strtotime($post['created_at'])) ?>
                        </div>
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="text-gray-600">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</body>
</html>
