<?php
declare(strict_types=1);

// Konfigurasi
$config = require __DIR__ . '/../config/app.php';
$baseDir = rtrim($config['uploads_dir'], '/'); // misal: /var/www/araska.id/storage/uploads

// Validasi parameter
if (!isset($_GET['file']) || trim($_GET['file']) === '') {
    http_response_code(400);
    exit('File parameter is required.');
}

// Bersihkan path untuk mencegah path traversal (../../)
$relativePath = ltrim(str_replace('\\', '/', $_GET['file']), '/');
$relativePath = preg_replace('/\.+[\/\\\\]/', '', $relativePath); // buang ../

// Path absolut
$filePath = $baseDir . '/' . $relativePath;

// Cek file
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

// Batasi ekstensi
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$allowedExt = ['pdf', 'docx'];
if (!in_array($ext, $allowedExt, true)) {
    http_response_code(403);
    exit('Access denied.');
}

// Kirim header untuk download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
header('X-Content-Type-Options: nosniff');

// Kirim isi file
readfile($filePath);
exit;
