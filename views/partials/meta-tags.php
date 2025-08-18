<!-- Site Title -->
<title><?= htmlspecialchars($meta['title'] ?? '') ?></title>

<!-- Meta Description -->
<meta name="description" content="<?= htmlspecialchars($meta['description'] ?? '') ?>">

<!-- Meta Keywords (if available) -->
<?php if (!empty($meta['keywords'])): ?>
<meta name="keywords" content="<?= htmlspecialchars($meta['keywords']) ?>">
<?php endif; ?>

<!-- Favicon -->
<?php if (!empty($siteSettings['site_favicon'])): ?>
<link rel="icon" type="image/png" href="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">
<link rel="shortcut icon" type="image/png" href="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">
<link rel="apple-touch-icon" href="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">
<?php endif; ?>

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">
<meta name="msapplication-TileImage" content="<?= htmlspecialchars($siteSettings['site_favicon']) ?>">
<meta name="msapplication-TileColor" content="#2563eb">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= htmlspecialchars($meta['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($pagination['canonicalUrl'] ?? $meta['canonical_url'] ?? '') ?>">
<meta property="og:type" content="<?= htmlspecialchars($meta['og_type'] ?? 'website') ?>">
<meta property="og:site_name" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">

<!-- Open Graph Image (if available) -->
<?php if (!empty($meta['og_image'])): ?>
<meta property="og:image" content="<?= htmlspecialchars($meta['og_image']) ?>">
<meta property="og:image:alt" content="<?= htmlspecialchars($meta['title'] ?? '') ?>">
<?php endif; ?>

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($meta['title'] ?? '') ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($meta['description'] ?? '') ?>">

<!-- Twitter Image (if available) -->
<?php if (!empty($meta['twitter_image'])): ?>
<meta name="twitter:image" content="<?= htmlspecialchars($meta['twitter_image']) ?>">
<?php endif; ?>

<!-- Canonical URL -->
<?php $canonical = $pagination['canonicalUrl'] ?? $meta['canonical_url'] ?? ''; ?>
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

<!-- Prev/Next (pagination-aware) -->
<?php if (!empty($pagination['prevUrl'])): ?>
  <link rel="prev" href="<?= htmlspecialchars($pagination['prevUrl']) ?>">
<?php endif; ?>
<?php if (!empty($pagination['nextUrl'])): ?>
  <link rel="next" href="<?= htmlspecialchars($pagination['nextUrl']) ?>">
<?php endif; ?>

<!-- Robots Meta -->
<?php $robots = $pagination['current'] > 1 ? 'noindex,follow' : ($meta['robots'] ?? 'index, follow'); ?>
<meta name="robots" content="<?= htmlspecialchars($robots) ?>">

<!-- Language and Locale -->
<meta property="og:locale" content="id_ID">
<meta name="language" content="Indonesian">

<!-- Additional Meta Tags -->
<meta name="author" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">
<meta name="generator" content="Custom CMS">

<!-- Theme Color for Mobile Browsers -->
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileColor" content="#ffffff">

<!-- Performance & Resource Hints -->
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//unpkg.com">

<!-- Preload Critical CSS -->
<link rel="preload" href="/css/style.css?v=<?= time() ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="/css/style.css?v=<?= time() ?>"></noscript>

<!-- CSS -->
<link href="/css/dark-mode.css?v=<?= time() ?>" rel="stylesheet" media="print" onload="this.media='all'">

<!-- PWA Service Worker Registration -->
<script>
// Dark Mode Implementation
(function() {
  // Check for saved theme preference or default to light mode
  const currentTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);

  // Create theme toggle button
  function createThemeToggle() {
    const toggleButton = document.createElement('button');
    toggleButton.className = 'theme-toggle';
    toggleButton.setAttribute('aria-label', 'Toggle dark mode');
    toggleButton.innerHTML = `
      <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
      </svg>
      <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
      </svg>
    `;

    toggleButton.addEventListener('click', function() {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      
      // Optional: Analytics tracking
      if (typeof gtag !== 'undefined') {
        gtag('event', 'theme_change', {
          'custom_parameter': newTheme
        });
      }
    });

    document.body.appendChild(toggleButton);
  }

  // Create toggle when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', createThemeToggle);
  } else {
    createThemeToggle();
  }
})();

// PWA Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        // Service worker registered successfully
      })
      .catch(registrationError => {
        // Service worker registration failed
      });
  });

  // Handle app install prompt
  let deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallPromotion();
  });

  function showInstallPromotion() {
    // Show install button (will implement UI later)
    const installBtn = document.createElement('button');
    installBtn.style.cssText = `
      position: fixed; 
      bottom: 20px; 
      right: 20px; 
      background: #2563eb; 
      color: white; 
      border: none; 
      padding: 12px 16px; 
      border-radius: 8px; 
      cursor: pointer; 
      font-size: 14px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      z-index: 1000;
      display: none;
    `;
    installBtn.textContent = '📱 Install App';
    installBtn.id = 'installBtn';
    
    installBtn.addEventListener('click', async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        installBtn.style.display = 'none';
      }
    });
    
    document.body.appendChild(installBtn);
    
    // Show button after 3 seconds if not already installed
    setTimeout(() => {
      if (!window.matchMedia('(display-mode: standalone)').matches) {
        installBtn.style.display = 'block';
      }
    }, 3000);
  }
}
</script>

<!-- Google Analytics (if configured) -->
<?php if (!empty($siteSettings['analytics']['ga4_measurement_id'])): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $siteSettings['analytics']['ga4_measurement_id'] ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= $siteSettings['analytics']['ga4_measurement_id'] ?>', {
    <?php if (!empty($siteSettings['analytics']['gtag_config'])): ?>
    <?php foreach ($siteSettings['analytics']['gtag_config'] as $key => $value): ?>
    <?= $key ?>: <?= is_bool($value) ? ($value ? 'true' : 'false') : (is_string($value) ? "'" . $value . "'" : $value) ?>,
    <?php endforeach; ?>
    <?php endif; ?>
  });
</script>
<?php endif; ?>

<!-- Google Search Console Verification (if configured) -->
<?php if (!empty($siteSettings['analytics']['google_search_console_verification'])): ?>
<meta name="google-site-verification" content="<?= htmlspecialchars($siteSettings['analytics']['google_search_console_verification']) ?>">
<?php endif; ?>
