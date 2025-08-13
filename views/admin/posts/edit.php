<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Edit Dokumen - <?=htmlspecialchars($siteSettings['site_name'])?></title>
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
            
            <?php include __DIR__ . '/../partials/navigation.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <div class="p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Tulis Post Baru</h1>
                    <p class="text-gray-600">Edit dokumen dan artikel yang sudah ada</p>
                </div>

                <form id="postForm" method="post" action="/admin/posts/<?= $post['id'] ?>" enctype="multipart/form-data" novalidate class="space-y-6">
                    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
                    <input type="hidden" name="id" value="<?= $post['id'] ?>">

                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                            <input id="title" name="title" required 
                                   value="<?= htmlspecialchars($post['title']) ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off">
                            <div class="mt-1 text-sm text-gray-500">
                                <span id="titleCount"><?= strlen($post['title']) ?>/255</span>
                            </div>
                        </div>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title (SEO, opsional)</label>
                            <input id="meta_title" name="meta_title" 
                                   value="<?= htmlspecialchars($post['meta_title']) ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off" 
                                   placeholder="Judul untuk title tag, default menggunakan judul di atas">
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug (otomatis, bisa diubah)</label>
                            <input id="slug" name="slug" 
                                   value="<?= htmlspecialchars($post['slug']) ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   maxlength="255" autocomplete="off" placeholder="otomatis dari judul">
                        </div>

                        <div>
                            <label for="category_select" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="category_select" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
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
                                      maxlength="500"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                            <div class="mt-1 text-sm text-gray-500">
                                <span id="excerptCount"><?= strlen($post['excerpt']) ?>/500</span>
                            </div>
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description (SEO, opsional)</label>
                            <textarea id="meta_description" name="meta_description" rows="2" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                      maxlength="500"
                                      placeholder="Deskripsi untuk meta description, default menggunakan excerpt"><?= htmlspecialchars($post['meta_description']) ?></textarea>
                        </div>

                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image (opsional)</label>
                            <?php if($post['featured_image']): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="Current featured image" class="max-w-xs h-32 object-cover rounded">
                                    <p class="text-sm text-gray-600 mt-1">Gambar saat ini (akan diganti jika upload baru)</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="featured_image" accept="image/*" id="featured_image"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div>
                            <label for="content_md" class="block text-sm font-medium text-gray-700">Konten (Markdown)</label>
                            <textarea id="content_md" name="content_md" rows="12"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($post['content_md']) ?></textarea>
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
                                Update Post
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

    // Initial counters (set to actual values for edit mode)
    titleCount.textContent = `${title.value.length}/255`;
    excerptCount.textContent = `${excerpt.value.length}/500`;
});
</script>
</body>
</html>
