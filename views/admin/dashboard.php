<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Dashboard - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm">
            <div class="p-6">
                <h1 class="text-xl font-bold text-gray-900"><?=htmlspecialchars($siteSettings['site_name'])?></h1>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            
            <?php include __DIR__ . '/partials/navigation.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">Ringkasan dan statistik situs Anda</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-3xl font-bold text-blue-600 mb-2"><?= count($posts) ?></div>
                        <div class="text-sm text-gray-600">Total Dokumen</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-3xl font-bold text-blue-600 mb-2"><?= count($categories ?? []) ?></div>
                        <div class="text-sm text-gray-600">Total Kategori</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Aksi Cepat</h3>
            <div class="flex flex-wrap gap-3">
                <a href="/admin/post/new" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Buat Dokumen Baru
                </a>
                <a href="/admin/categories" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                    Kelola Kategori
                </a>
                <a href="/admin/settings" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                    Pengaturan Site
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Dokumen Terbaru</h3>
            </div>
            <div class="p-6">
                <?php if (empty($posts)): ?>
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada dokumen</h3>
                        <p class="text-sm text-gray-500 mb-4">Mulai dengan membuat dokumen pertama Anda.</p>
                        <a href="/admin/post/new" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Buat Dokumen Baru
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                    <?php foreach (array_slice($posts, 0, 10) as $p): ?>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex-1 min-w-0">
                                <a href="/post/<?=htmlspecialchars($p['slug'])?>" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    <?=htmlspecialchars($p['title'])?>
                                </a>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?=date('j F Y', strtotime($p['created_at']))?>
                                    <?php if (!empty($p['category_name'])): ?>
                                        • <?=htmlspecialchars($p['category_name'])?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ml-4">
                              <a href="/admin/posts/<?= (int)$p['id'] ?>/edit" 
                                class="px-3 py-1 mx-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100">
                                Edit
                              </a>
                                <form method="post" action="/admin/post/delete" class="inline" 
                                      onsubmit="return confirm('Hapus dokumen ini? Tindakan tidak bisa dibatalkan.');">
                                    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <button type="submit" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <?php if (count($posts) > 10): ?>
                        <div class="text-center mt-6">
                            <a href="/admin/posts" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                                Lihat Semua Dokumen
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
            </div>
        </div>
    </div>
</body>
</html>
