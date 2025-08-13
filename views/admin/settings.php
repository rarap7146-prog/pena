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
            <nav class="mt-6">
                <a href="/admin" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    </svg>
                    Dashboard
                </a>
                <a href="/admin/post/new" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tulis Baru
                </a>
                <a href="/admin/posts" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Kelola Post
                </a>
                <a href="/admin/categories" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Kelola Kategori
                </a>
                <a href="/admin/settings" class="flex items-center px-6 py-3 text-blue-700 bg-blue-50 border-r-2 border-blue-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>
            </nav>
            <div class="absolute bottom-0 w-64 p-6 border-t border-gray-200">
                <a href="/" class="flex items-center text-gray-600 hover:text-gray-900 mb-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Situs
                </a>
                <form method="post" action="/logout" class="inline">
                    <button type="submit" class="flex items-center text-red-600 hover:text-red-800 text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
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
