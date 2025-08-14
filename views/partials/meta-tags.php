<!-- Site Title -->
<title><?= htmlspecialchars($meta['title']) ?></title>

<!-- Meta Description -->
<meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">

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

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= htmlspecialchars($meta['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($meta['canonical_url']) ?>">
<meta property="og:type" content="<?= htmlspecialchars($meta['og_type'] ?? 'website') ?>">
<meta property="og:site_name" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">

<!-- Open Graph Image (if available) -->
<?php if (!empty($meta['og_image'])): ?>
<meta property="og:image" content="<?= htmlspecialchars($meta['og_image']) ?>">
<meta property="og:image:alt" content="<?= htmlspecialchars($meta['title']) ?>">
<?php endif; ?>

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($meta['title']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($meta['description']) ?>">

<!-- Twitter Image (if available) -->
<?php if (!empty($meta['twitter_image'])): ?>
<meta name="twitter:image" content="<?= htmlspecialchars($meta['twitter_image']) ?>">
<?php endif; ?>

<!-- Canonical URL -->
<link rel="canonical" href="<?= htmlspecialchars($meta['canonical_url']) ?>">

<!-- Robots Meta -->
<meta name="robots" content="<?= htmlspecialchars($meta['robots'] ?? 'index, follow') ?>">

<!-- Language and Locale -->
<meta property="og:locale" content="id_ID">
<meta name="language" content="Indonesian">

<!-- Additional Meta Tags -->
<meta name="author" content="<?= htmlspecialchars($siteSettings['site_name']) ?>">
<meta name="generator" content="Custom CMS">

<!-- Theme Color for Mobile Browsers -->
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileColor" content="#ffffff">

<!-- CSS -->
<link href="/css/style.css?v=<?= time() ?>" rel="stylesheet">

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
