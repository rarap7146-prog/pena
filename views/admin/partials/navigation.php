<?php
/**
 * Admin Navigation Component
 * Dynamic navigation bar for admin area
 */

// Get current URI to highlight active menu
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

// Define navigation menu items
$navigationItems = [
    [
        'url' => '/admin',
        'label' => 'Dashboard',
        'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
        'active_pattern' => '/^\/admin\/?$/'
    ],
    [
        'url' => '/admin/post/new',
        'label' => 'Tulis Baru',
        'icon' => 'M12 4v16m8-8H4',
        'active_pattern' => '/^\/admin\/post\/new/'
    ],
    [
        'url' => '/admin/posts',
        'label' => 'Kelola Post',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'active_pattern' => '/^\/admin\/posts/'
    ],
    [
        'url' => '/admin/categories',
        'label' => 'Kelola Kategori',
        'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'active_pattern' => '/^\/admin\/categories/'
    ],
    [
        'url' => '/admin/analytics',
        'label' => 'Analytics',
        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'active_pattern' => '/^\/admin\/analytics/'
    ],
    [
        'url' => '/admin/settings',
        'label' => 'Pengaturan',
        'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'active_pattern' => '/^\/admin\/settings/'
    ]
];

// Function to check if current menu item is active
function isMenuActive($pattern, $currentUri) {
    return preg_match($pattern, $currentUri);
}
?>

<nav class="mt-6">
    <?php foreach ($navigationItems as $item): ?>
        <?php 
        $isActive = isMenuActive($item['active_pattern'], $currentUri);
        $activeClass = $isActive ? 'text-blue-700 bg-blue-50 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50';
        ?>
        <a href="<?= htmlspecialchars($item['url']) ?>" 
           class="flex items-center px-6 py-3 <?= $activeClass ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= htmlspecialchars($item['icon']) ?>" />
            </svg>
            <?= htmlspecialchars($item['label']) ?>
        </a>
    <?php endforeach; ?>
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
