<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Pengaturan - <?=htmlspecialchars($siteSettings['site_name'])?></title>
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
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Pengaturan Situs</h1>
                    <p class="text-gray-600">Kelola konfigurasi dan pengaturan situs Anda</p>
                </div>

                <?php if (isset($_GET['saved'])): ?>
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Pengaturan berhasil disimpan!</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Konfigurasi Website</h2>
                
                <form method="post" action="/admin/settings" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Nama Website</label>
                        <input type="text" id="site_name" name="site_name" 
                               value="<?=htmlspecialchars($settings['site_name'])?>" 
                               required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-2 text-sm text-gray-500">Nama yang akan tampil di tab browser dan header website</p>
                    </div>

                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Deskripsi Website</label>
                        <textarea id="site_description" name="site_description" 
                                  rows="3"
                                  placeholder="Deskripsi singkat tentang website Anda"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?=htmlspecialchars($settings['site_description'])?></textarea>
                        <p class="mt-2 text-sm text-gray-500">Deskripsi yang akan tampil di hasil pencarian Google</p>
                    </div>

                    <!-- Homepage Settings Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-md font-medium text-gray-900 mb-4">Pengaturan Halaman Utama</h3>
                        <p class="text-sm text-gray-600 mb-4">Atur title dan meta description khusus untuk halaman utama. Kosongkan untuk menggunakan pengaturan website umum di atas.</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="home_title" class="block text-sm font-medium text-gray-700">Home Title</label>
                                <input type="text" id="home_title" name="home_title" 
                                       value="<?=htmlspecialchars($settings['home']['title'] ?? '')?>" 
                                       placeholder="Kosongkan untuk menggunakan nama website"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Title khusus untuk halaman utama (opsional)</p>
                            </div>

                            <div>
                                <label for="home_meta_description" class="block text-sm font-medium text-gray-700">Home Meta Description</label>
                                <textarea id="home_meta_description" name="home_meta_description" 
                                          rows="3"
                                          placeholder="Kosongkan untuk menggunakan deskripsi website"
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?=htmlspecialchars($settings['home']['meta_description'] ?? '')?></textarea>
                                <p class="mt-1 text-sm text-gray-500">Meta description khusus untuk halaman utama (opsional)</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700">Favicon</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <input type="file" id="site_favicon" name="site_favicon" 
                                   accept="image/*,.ico"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Upload file favicon (.ico, .png, .jpg) maksimal 2MB</p>
                        <?php if (!empty($settings['site_favicon']) && $settings['site_favicon'] !== '/favicon.ico'): ?>
                            <div class="mt-2 flex items-center space-x-2">
                                <img src="<?=htmlspecialchars($settings['site_favicon'])?>" alt="Current favicon" class="w-4 h-4">
                                <span class="text-sm text-gray-600">Favicon saat ini</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="/admin" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Kembali
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
            </div>
        </div>
    </div>
</body>
</html>
