<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title><?= isset($category) ? 'Edit Kategori' : 'Kategori Baru' ?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
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
            
            <?php include __DIR__ . '/../partials/navigation.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <div class="p-8">
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <a href="/admin/categories" class="text-blue-600 hover:text-blue-800 text-sm">← Kelola Kategori</a>
                        <span class="text-gray-400">/</span>
                        <span class="text-sm text-gray-600"><?= isset($category) ? 'Edit Kategori' : 'Kategori Baru' ?></span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= isset($category) ? 'Edit Kategori' : 'Kategori Baru' ?></h1>
                    <p class="text-gray-600"><?= isset($category) ? 'Perbarui informasi kategori' : 'Tambahkan kategori baru untuk mengorganisir konten' ?></p>
                </div>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($_SESSION['error']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form method="post" action="<?= isset($category) ? "/admin/categories/{$category['id']}" : '/admin/categories' ?>" class="space-y-6">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" id="name" name="name" 
                           value="<?= htmlspecialchars($category['name'] ?? '') ?>" 
                           required maxlength="100"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input type="text" id="slug" name="slug" 
                           value="<?= htmlspecialchars($category['slug'] ?? '') ?>" 
                           maxlength="100" pattern="[a-z0-9\-]+"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-2 text-sm text-gray-500">Opsional, akan dibuat otomatis dari nama jika dikosongkan</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    <p class="mt-2 text-sm text-gray-500">Opsional, deskripsi singkat tentang kategori ini</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="/admin/categories" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Auto-generate slug from name
    document.querySelector('[name="name"]').addEventListener('input', function(e) {
        const slugInput = document.querySelector('[name="slug"]');
        if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }
    });

    // Mark slug as manually edited if user types in it
    document.querySelector('[name="slug"]').addEventListener('input', function(e) {
        e.target.dataset.autoGenerated = 'false';
    });
    </script>
            </div>
        </div>
    </div>
</body>
</html>
