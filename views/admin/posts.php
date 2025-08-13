<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Kelola Post - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Kelola Post</h1>
                        <p class="text-gray-600">Daftar semua dokumen dan artikel</p>
                    </div>
                    <a href="/admin/new" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Tulis Post Baru
                    </a>
                </div>

                <?php if (empty($posts)): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Dokumen</h3>
                <p class="text-gray-500 mb-6">Mulai dengan membuat dokumen pertama Anda.</p>
                <a href="/admin/post/new" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Buat Dokumen Baru
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Judul
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($posts as $post): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <a href="/post/<?=htmlspecialchars($post['slug'])?>" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                                <?=htmlspecialchars($post['title'])?>
                                            </a>
                                            <?php if (!empty($post['excerpt'])): ?>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <?=htmlspecialchars(substr($post['excerpt'], 0, 100))?>
                                                    <?php if (strlen($post['excerpt']) > 100): ?>...<?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($post['category_name'])): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?=htmlspecialchars($post['category_name'])?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">Tanpa kategori</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?=date('j M Y', strtotime($post['created_at']))?></div>
                                        <div class="text-sm text-gray-500"><?=date('H:i', strtotime($post['created_at']))?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="post" action="/admin/post/delete" class="inline" 
                                              onsubmit="return confirm('Hapus dokumen ini? Tindakan tidak bisa dibatalkan.');">
                                            <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
                                            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                                            <button type="submit" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>