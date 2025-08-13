<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Buat Dokumen - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- EasyMDE CSS -->
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
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
                <a href="/admin/post/new" class="flex items-center px-6 py-3 text-blue-700 bg-blue-50 border-r-2 border-blue-700">
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
                <a href="/admin/settings" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>
            </nav>
            <div class="absolute bottom-0 w-64 p-6">
                <a href="/" class="flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Situs
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Tulis Post Baru</h1>
                    <p class="text-gray-600">Buat dokumen dan artikel baru untuk situs Anda</p>
                </div>

                <form id="postForm" method="post" action="/admin/post" enctype="multipart/form-data" novalidate class="space-y-6">
                    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">

                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                            <input id="title" name="title" required 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off">
                            <div class="mt-1 text-sm text-gray-500">
                                <span id="titleCount">0/255</span>
                            </div>
                        </div>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title (SEO, opsional)</label>
                            <input id="meta_title" name="meta_title" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off" 
                                   placeholder="Judul untuk title tag, default menggunakan judul di atas">
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug (otomatis, bisa diubah)</label>
                            <input id="slug" name="slug" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off" placeholder="otomatis dari judul">
                        </div>

                        <div>
                            <label for="category_select" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="category_select" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2">
                                <button type="button" id="showCategoryForm" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    + Tambah kategori baru
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt (opsional)</label>
                            <textarea id="excerpt" name="excerpt" rows="2" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                      maxlength="500"></textarea>
                            <div class="mt-1 text-sm text-gray-500">
                                <span id="excerptCount">0/500</span>
                            </div>
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description (SEO, opsional)</label>
                            <textarea id="meta_description" name="meta_description" rows="2" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                      maxlength="500"
                                      placeholder="Deskripsi untuk meta description, default menggunakan excerpt"></textarea>
                        </div>

                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image (opsional)</label>
                            <input type="file" name="featured_image" accept="image/*" id="featured_image"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div>
                            <label for="content_md" class="block text-sm font-medium text-gray-700">Konten (Markdown)</label>
                            <textarea id="content_md" name="content_md" rows="12"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">Upload file asli (PDF/DOCX, opsional, maks 5MB)</label>
                            <input id="file" type="file" name="file" accept=".pdf,.docx"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <div class="mt-1 text-sm text-gray-500">
                                <span id="fileHint">(.pdf, .docx — ≤ 5MB)</span>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Simpan Post
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Form Kategori -->
    <div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display:none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Kategori Baru</h3>
                <form id="categoryForm" action="/admin/categories" method="post" class="space-y-4">
                    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
                    
                    <div>
                        <label for="category_name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" name="name" id="category_name" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                               maxlength="100">
                    </div>

                    <div>
                        <label for="category_description" class="block text-sm font-medium text-gray-700">Deskripsi (opsional)</label>
                        <textarea name="description" id="category_description" rows="2"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                  maxlength="500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" id="closeCategoryModal" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EasyMDE JavaScript -->
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const title = document.getElementById('title');
    const slug = document.getElementById('slug');
    const excerpt = document.getElementById('excerpt');
    const titleCount = document.getElementById('titleCount');
    const excerptCount = document.getElementById('excerptCount');
    const form = document.getElementById('postForm');
    const file = document.getElementById('file');
    
    // Category modal elements
    const showCategoryForm = document.getElementById('showCategoryForm');
    const categoryModal = document.getElementById('categoryModal');
    const closeCategoryModal = document.getElementById('closeCategoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const categorySelect = document.getElementById('category_select');

    let slugTouched = false;

    function toSlug(str) {
        return str.toLowerCase()
                  .replace(/[^\w\s-]/g, '')
                  .replace(/[\s_-]+/g, '-')
                  .replace(/^-+|-+$/g, '');
    }

    // Initialize EasyMDE
    const easyMDE = new EasyMDE({
        element: document.getElementById('content_md'),
        spellChecker: false,
        autosave: {
            enabled: true,
            uniqueId: 'post_content',
            delay: 1000,
        },
        uploadImage: true,
        imageUploadEndpoint: '/upload-image.php',
        imageCSRFToken: '<?= htmlspecialchars($csrf) ?>',
        imageCSRFHeader: 'X-CSRF-Token',
        imageMaxSize: 5 * 1024 * 1024, // 5MB
        imageAccept: 'image/png, image/jpeg, image/gif, image/webp',
        imageUploadFunction: (file, onSuccess, onError) => {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('csrf', '<?= htmlspecialchars($csrf) ?>');

            fetch('/upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    onSuccess(result.url);
                } else {
                    onError(result.error);
                }
            })
            .catch(error => {
                onError('Upload failed');
            });
        },
        renderingConfig: {
            singleLineBreaks: false,
            codeSyntaxHighlighting: true,
        }
    });

    // Handle paste images
    document.addEventListener('paste', function(e) {
        if (!e.clipboardData || !e.clipboardData.items) return;
        
        const items = e.clipboardData.items;
        
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                const formData = new FormData();
                formData.append('image', file);
                formData.append('csrf', '<?= htmlspecialchars($csrf) ?>');
                
                fetch('/upload-image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const imageMarkdown = `![](${result.url})`;
                        easyMDE.codemirror.replaceSelection(imageMarkdown);
                    }
                })
                .catch(error => {
                    console.error('Image paste upload failed:', error);
                });
            }
        }
    });

    // Slug auto-generation
    slug.addEventListener('input', () => {
        slugTouched = true;
    });

    title.addEventListener('input', () => {
        titleCount.textContent = `${title.value.length}/255`;
        if (!slugTouched) slug.value = toSlug(title.value);
    });

    excerpt.addEventListener('input', () => {
        excerptCount.textContent = `${excerpt.value.length}/500`;
    });

    // Category modal handlers
    showCategoryForm.addEventListener('click', (e) => {
        e.preventDefault();
        categoryModal.style.display = 'block';
    });

    closeCategoryModal.addEventListener('click', () => {
        categoryModal.style.display = 'none';
    });

    // Close modal when clicking outside
    categoryModal.addEventListener('click', (e) => {
        if (e.target === categoryModal) {
            categoryModal.style.display = 'none';
        }
    });

    // Handle category form submission
    categoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const categoryName = document.getElementById('category_name').value;
        
        // Client-side validation
        if (!categoryName || categoryName.trim() === '') {
            alert('Nama kategori harus diisi!');
            return;
        }
        
        const formData = new FormData(categoryForm);
        
        try {
            const response = await fetch('/admin/categories', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (response.ok) {
                const result = await response.json();
                
                if (result.success) {
                    // Add new category to dropdown
                    const option = document.createElement('option');
                    option.value = result.category.id;
                    option.textContent = result.category.name;
                    option.selected = true;
                    categorySelect.appendChild(option);
                    
                    // Close modal and reset form
                    categoryModal.style.display = 'none';
                    categoryForm.reset();
                    
                    alert('Kategori berhasil ditambahkan!');
                } else {
                    alert('Error: ' + (result.error || 'Gagal membuat kategori'));
                }
            } else {
                alert('HTTP Error: ' + response.status);
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
        }
    });

    // Client-side file checks (size & extension)
    form.addEventListener('submit', (e) => {
        const f = file.files && file.files[0];
        if (f) {
            const okExt = /\.(pdf|docx)$/i.test(f.name);
            const okSize = f.size <= 5 * 1024 * 1024; // 5MB
            if (!okExt) {
                e.preventDefault();
                alert('Tipe file tidak diizinkan. Hanya PDF atau DOCX.');
                return;
            }
            if (!okSize) {
                e.preventDefault();
                alert('Ukuran file maksimal 5MB.');
                return;
            }
        }
        // normalize slug just before submit
        slug.value = toSlug(slug.value || title.value);
    });

    // Initial counters
    titleCount.textContent = `0/255`;
    excerptCount.textContent = `0/500`;
});
</script>
</body>
</html>
