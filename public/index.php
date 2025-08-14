<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

function run($ctrl, $method, ...$args) {
  require_once __DIR__ . "/../app/controllers/{$ctrl}.php";
  (new $ctrl)->{$method}(...$args);
  exit;
}

/* Public pages */
if ($uri === '/' && $method === 'GET') {
  run('PostController', 'index');
}

if (preg_match('~^/post/([\w-]+)$~', $uri, $m) && $method === 'GET') {
  run('PostController', 'show', $m[1]);
}

if ($uri === '/categories' && $method === 'GET') {
  run('CategoryController', 'listAll');
}

if (preg_match('~^/category/([\w-]+)$~', $uri, $m) && $method === 'GET') {
  run('CategoryController', 'posts', $m[1]);
}

/* Search */
if ($uri === '/search' && $method === 'GET') {
  run('SearchController', 'search');
}
if ($uri === '/search/suggestions' && $method === 'GET') {
  run('SearchController', 'suggestions');
}

/* SEO Files */
if ($uri === '/sitemap.xml' && $method === 'GET') {
  run('SitemapController', 'xml');
}

/* API Endpoints */
if ($uri === '/api/performance' && $method === 'POST') {
  run('PerformanceController', 'logMetric');
}
if ($uri === '/api/performance' && $method === 'GET') {
  run('PerformanceController', 'getMetrics');
}

/* Auth */
if ($uri === '/login' && $method === 'GET') {
  run('AuthController', 'loginForm');
}
if ($uri === '/login' && $method === 'POST') {
  run('AuthController', 'login');
}
if ($uri === '/logout' && $method === 'POST') {
  run('AuthController', 'logout');
}

/* Generated downloads (specific) */
if ($uri === '/download/pdf'  && $method === 'GET') {
  run('ExportController', 'pdf');
}
if ($uri === '/download/docx' && $method === 'GET') {
  run('ExportController', 'docx');
}

/* File download (generic) - keep after specific routes above */
if (preg_match('~^/download/(.+)$~', $uri, $m) && $method === 'GET') {
  run('DownloadController', 'file', $m[1]);
}

/* Admin */
if ($uri === '/admin' && $method === 'GET') {
  run('AdminController', 'dashboard');
}
if ($uri === '/admin/posts' && $method === 'GET') {
  run('AdminController', 'posts');
}
if ($uri === '/admin/settings' && $method === 'GET') {
  run('AdminController', 'settings');
}
if ($uri === '/admin/settings' && $method === 'POST') {
  run('AdminController', 'updateSettings');
}
if ($uri === '/admin/analytics' && $method === 'GET') {
  run('AdminController', 'analytics');
}
if ($uri === '/admin/analytics' && $method === 'POST') {
  run('AdminController', 'updateAnalytics');
}

/* Categories */
if ($uri === '/admin/categories' && $method === 'GET') {
  run('CategoryController', 'index');
}
if ($uri === '/admin/categories/new' && $method === 'GET') {
  run('CategoryController', 'create');
}
if ($uri === '/admin/categories' && $method === 'POST') {
  run('CategoryController', 'store');
}
if (preg_match('~^/admin/categories/(\d+)/edit$~', $uri, $m) && $method === 'GET') {
  run('CategoryController', 'edit', (int)$m[1]);
}
if (preg_match('~^/admin/categories/(\d+)$~', $uri, $m) && $method === 'POST') {
  run('CategoryController', 'update', (int)$m[1]);
}
if (preg_match('~^/admin/categories/(\d+)/delete$~', $uri, $m) && $method === 'POST') {
  run('CategoryController', 'delete', (int)$m[1]);
}
if ($uri === '/admin/post/new' && $method === 'GET') {
  run('AdminController', 'createForm');
}
if ($uri === '/admin/post' && $method === 'POST') {
  run('AdminController', 'store');
}
if (preg_match('~^/admin/posts/(\d+)/edit$~', $uri, $m) && $method === 'GET') {
  run('AdminController', 'editPost', (int)$m[1]);
}
if (preg_match('~^/admin/posts/(\d+)$~', $uri, $m) && $method === 'POST') {
  run('AdminController', 'updatePost', (int)$m[1]);
}
if ($uri === '/admin/post/delete' && $method === 'POST') {
    run('AdminController', 'delete');
}

/* Fallback */
http_response_code(404);
echo '404';
