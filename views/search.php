<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    require_once __DIR__ . '/../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    ?>
    <title>Pencarian: <?=htmlspecialchars($searchData['query'])?> - <?=htmlspecialchars($siteSettings['site_name'])?></title>
    <meta name="description" content="Hasil pencarian untuk '<?=htmlspecialchars($searchData['query'])?>' di <?=htmlspecialchars($siteSettings['site_name'])?>">
    
    <!-- OpenGraph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Pencarian: <?=htmlspecialchars($searchData['query'])?>">
    <meta property="og:description" content="Hasil pencarian untuk '<?=htmlspecialchars($searchData['query'])?>' di <?=htmlspecialchars($siteSettings['site_name'])?>">
    <meta property="og:url" content="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/search?q=<?=urlencode($searchData['query'])?>">
    <meta property="og:site_name" content="<?=htmlspecialchars($siteSettings['site_name'])?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?=htmlspecialchars($siteSettings['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST'])?>/search?q=<?=urlencode($searchData['query'])?>">
    
    <link rel="icon" href="<?=htmlspecialchars($siteSettings['site_favicon'])?>">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="max-w-4xl mx-auto px-4 py-8">
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
                           autocomplete="off">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
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
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($searchData['totalPages'] > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-1">
                        <?php if ($searchData['currentPage'] > 1): ?>
                            <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=$searchData['currentPage'] - 1?>" 
                               class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                ← Sebelumnya
                            </a>
                        <?php endif; ?>

                        <?php 
                        $start = max(1, $searchData['currentPage'] - 2);
                        $end = min($searchData['totalPages'], $searchData['currentPage'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i == $searchData['currentPage']): ?>
                                <span class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-blue-600">
                                    <?=$i?>
                                </span>
                            <?php else: ?>
                                <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=$i?>" 
                                   class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                    <?=$i?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($searchData['currentPage'] < $searchData['totalPages']): ?>
                            <a href="/search?q=<?=urlencode($searchData['query'])?>&page=<?=$searchData['currentPage'] + 1?>" 
                               class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                Berikutnya →
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Results -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-6">
                    <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Tidak ada hasil ditemukan</h3>
                <p class="text-gray-600 mb-8">Tidak ditemukan artikel yang sesuai dengan "<span class="font-semibold"><?=htmlspecialchars($searchData['query'])?></span>"</p>
                
                <!-- Search Tips -->
                <div class="bg-gray-50 rounded-lg p-6 text-left max-w-md mx-auto">
                    <h4 class="font-semibold text-gray-900 mb-3">💡 Tips pencarian:</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="font-medium mr-2">•</span>
                            <span>Periksa ejaan kata kunci</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium mr-2">•</span>
                            <span>Gunakan kata kunci yang lebih umum</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium mr-2">•</span>
                            <span>Coba sinonim dari kata yang dicari</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-medium mr-2">•</span>
                            <span>Kurangi jumlah kata kunci</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mt-8">
                    <a href="/" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Search highlighting function -->
    <?php 
    function highlightSearchQuery($text, $query) {
        if (empty($query)) return $text;
        
        $highlighted = preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark class="bg-yellow-200 px-1 rounded">$1</mark>',
            $text
        );
        
        return $highlighted;
    }
    ?>
</body>
</html>
