<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();

    // Search highlighting function
    function highlightSearchQuery($text, $query) {
        if (empty($query)) return $text;
        
        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark class="bg-yellow-200 px-1 rounded">$1</mark>',
            $text
        );
        
        return $highlighted;
    }

    // Generate meta tags for search
    $meta = array(
        'title' => 'Pencarian: ' . $searchData['query'] . ' - ' . $siteSettings['site_name'],
        'description' => "Hasil pencarian untuk '" . $searchData['query'] . "' di " . $siteSettings['site_name'],
        'canonical_url' => ($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) . '/search?q=' . urlencode($searchData['query']),
        'og_type' => 'website',
        'robots' => 'noindex, follow'
    );

    include __DIR__ . '/partials/meta-tags.php';
    ?>
</head>
<body class="bg-gray-50 font-sans">
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Search Results Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Hasil Pencarian</h1>
                <a href="/" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Kembali ke Beranda
                </a>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <p class="text-gray-600">
                    Menampilkan <span class="font-semibold"><?= $searchData['total'] ?></span> hasil 
                    untuk "<span class="font-semibold text-gray-900"><?=htmlspecialchars($searchData['query'])?></span>"
                </p>
            </div>
        </div>

        <!-- Search Results -->
        <?php if (!empty($searchData['results'])): ?>
            <div class="grid gap-6">
                <?php foreach($searchData['results'] as $post): ?>
                    <article class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300">
                        <!-- Category -->
                        <?php if (!empty($post['category_name'])): ?>
                            <div class="mb-3">
                                <a href="/category/<?=htmlspecialchars($post['category_slug'])?>" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors">
                                    <?=htmlspecialchars($post['category_name'])?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Title -->
                        <h2 class="text-xl font-semibold mb-3">
                            <a href="/post/<?=htmlspecialchars($post['slug'])?>" 
                               class="text-gray-900 hover:text-blue-600 hover:underline">
                                <?=highlightSearchQuery(htmlspecialchars($post['title']), $searchData['query'])?>
                            </a>
                        </h2>

                        <!-- Excerpt -->
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="text-gray-600 mb-4 leading-relaxed">
                                <?=highlightSearchQuery(htmlspecialchars($post['excerpt']), $searchData['query'])?>
                            </p>
                        <?php endif; ?>

                        <!-- Meta -->
                        <div class="flex items-center text-sm text-gray-500">
                            <time datetime="<?=date('Y-m-d', strtotime($post['created_at']))?>">
                                <?=date('d M Y', strtotime($post['created_at']))?>
                            </time>
                            <?php if (!empty($post['download_count'])): ?>
                                <span class="mx-2">•</span>
                                <span><?=number_format($post['download_count'])?> unduhan</span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($searchData['totalPages'] > 1): ?>
                <div class="mt-12">
                    <nav class="flex justify-center">
                        <div class="flex space-x-2">
                            <?php if ($searchData['currentPage'] > 1): ?>
                                <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=($searchData['currentPage'] - 1)?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    ← Sebelumnya
                                </a>
                            <?php endif; ?>

                            <?php 
                            $startPage = max(1, $searchData['currentPage'] - 2);
                            $endPage = min($searchData['totalPages'], $searchData['currentPage'] + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i == $searchData['currentPage']): ?>
                                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
                                        <?=$i?>
                                    </span>
                                <?php else: ?>
                                    <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=$i?>" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        <?=$i?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($searchData['currentPage'] < $searchData['totalPages']): ?>
                                <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=($searchData['currentPage'] + 1)?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    Selanjutnya →
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Results State -->
            <div class="text-center py-16">
                <div class="bg-white rounded-xl border border-gray-200 p-12">
                    <div class="text-gray-400 mb-6">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                        Tidak ada hasil ditemukan
                    </h3>
                    
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Maaf, tidak ada artikel yang cocok dengan pencarian "<strong><?=htmlspecialchars($searchData['query'])?></strong>". 
                        Coba gunakan kata kunci yang berbeda.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="/" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                ← Kembali ke Beranda
                            </a>
                            <a href="/categories" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                                Jelajahi Kategori
                            </a>
                        </div>
                        
                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Tips pencarian:</h4>
                            <ul class="text-sm text-gray-600 space-y-1 max-w-md mx-auto text-left">
                                <li>• Coba gunakan kata kunci yang lebih umum</li>
                                <li>• Periksa ejaan kata kunci</li>
                                <li>• Gunakan sinonim atau kata-kata terkait</li>
                                <li>• Kurangi jumlah kata kunci</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
