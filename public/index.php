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
if ($uri === '/admin/post/new' && $method === 'GET') {
  run('AdminController', 'createForm');
}
if ($uri === '/admin/post' && $method === 'POST') {
  run('AdminController', 'store');
}
if ($uri === '/admin/post/delete' && $method === 'POST') {
    run('AdminController', 'delete');
}

/* Fallback */
http_response_code(404);
echo '404';
