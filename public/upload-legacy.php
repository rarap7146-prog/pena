<?php
declare(strict_types=1);
// Set lokasi penyimpanan session ke folder dalam project
session_save_path(__DIR__ . '/../storage/sessions');

// Pastikan foldernya ada dan punya permission benar
if (!is_dir(__DIR__ . '/../storage/sessions')) {
    mkdir(__DIR__ . '/../storage/sessions', 0700, true);
}
session_start();

$config  = require __DIR__ . '/../config/app.php';

// ---- FLASH MESSAGE (PRG) ----
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// ---- CSRF TOKEN ----
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf'];

// ---- HELPERS ----
function allowedExtension(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['pdf', 'docx'], true);
}

function detectMime(string $tmpPath): string {
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $m = $finfo->file($tmpPath);
        if ($m) return $m;
    }
    if (function_exists('mime_content_type')) {
        return mime_content_type($tmpPath) ?: 'application/octet-stream';
    }
    return 'application/octet-stream';
}

function safeDownloadUrl(string $baseUrl, array $segments): string {
    $encoded = array_map('rawurlencode', $segments);
    return rtrim($baseUrl, '/') . '/' . implode('/', $encoded);
}

function listUploadedFiles(string $dir, string $baseUrl): void {
    if (!is_dir($dir)) return;

    // Pagination
    $page  = (int)($_GET['page']  ?? 1); if ($page < 1) $page = 1;
    $limit = (int)($_GET['limit'] ?? 10); if ($limit < 1) $limit = 10; if ($limit > 50) $limit = 50;

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    $files = [];
    foreach ($it as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['pdf','docx'], true)) continue;

            $relativePath = ltrim(str_replace(['\\', $dir], ['/', ''], $file->getPathname()), '/');
            $segments = array_values(array_filter(explode('/', $relativePath), 'strlen'));
            $url = safeDownloadUrl($baseUrl, $segments);

            $files[] = [
                'name'  => $file->getFilename(),
                'url'   => $url,
                'mtime' => $file->getMTime(),
            ];
        }
    }
    if (!$files) return;

    // Sort newest first
    usort($files, fn($a,$b) => $b['mtime'] <=> $a['mtime']);

    $total = count($files);
    $totalPages = (int)ceil($total / $limit);
    if ($page > $totalPages) $page = $totalPages ?: 1;
    $offset = ($page - 1) * $limit;
    $slice = array_slice($files, $offset, $limit);

    echo "<h2>Daftar File yang Sudah Diupload</h2><ul>";
    foreach ($slice as $f) {
        $name = htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8');
        $url  = htmlspecialchars($f['url'],  ENT_QUOTES, 'UTF-8');
        echo "<li><a href=\"{$url}\" target=\"_blank\">{$name}</a></li>";
    }
    echo "</ul>";

    // pager
    if ($totalPages > 1) {
        $q = $_GET; unset($q['page']); unset($q['limit']);
        $qs = fn($p) => ($q ? http_build_query($q) . '&' : '') . 'page=' . $p . '&limit=' . $limit;
        echo '<nav>';
        if ($page > 1)   echo '<a href="?'.$qs($page-1).'">← Prev</a> ';
        echo " Hal. {$page} / {$totalPages} ";
        if ($page < $totalPages) echo ' <a href="?'.$qs($page+1).'">Next →</a>';
        echo '</nav>';
    }
}

// ---- HANDLE UPLOAD (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', (string)$_POST['csrf'])) {
        http_response_code(403);
        $_SESSION['flash_message'] = '<div style="color:red">Sesi kedaluwarsa. Muat ulang halaman.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    if (!isset($_FILES['file'])) {
        http_response_code(400);
        $_SESSION['flash_message'] = '<div style="color:red">Tidak ada file yang diunggah.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    // Error codes
    $err = $_FILES['file']['error'];
    if ($err !== UPLOAD_ERR_OK) {
        $msg = match ($err) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file terlalu besar.',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
            default => 'Terjadi kesalahan saat mengunggah file.',
        };
        http_response_code(400);
        $_SESSION['flash_message'] = '<div style="color:red">'.htmlspecialchars($msg, ENT_QUOTES, 'UTF-8').'</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    // Size limit 5MB
    $maxSize = 5 * 1024 * 1024;
    if (($_FILES['file']['size'] ?? 0) > $maxSize) {
        http_response_code(400);
        $_SESSION['flash_message'] = '<div style="color:red">Ukuran file maksimal 5MB.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    $origName = basename($_FILES['file']['name'] ?? '');
    if (!allowedExtension($origName)) {
        http_response_code(415);
        $_SESSION['flash_message'] = '<div style="color:red">Ekstensi tidak diizinkan (hanya .pdf, .docx).</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    $tmpPath = $_FILES['file']['tmp_name'];
    $mime = detectMime($tmpPath);
    $allowedMime = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    if (!in_array($mime, $allowedMime, true)) {
        http_response_code(415);
        $_SESSION['flash_message'] = '<div style="color:red">Tipe file tidak diizinkan.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    // Subfolder tanggal
    $datePath  = date('Y/m/d');
    $uploadDir = rtrim($config['uploads_dir'], '/') . '/' . $datePath;
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        http_response_code(500);
        $_SESSION['flash_message'] = '<div style="color:red">Gagal membuat folder upload.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    // Nama file aman + unik
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $safeBase = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
    $rand     = bin2hex(random_bytes(8));
    $safeName = "{$rand}_{$safeBase}.{$ext}";
    $target   = $uploadDir . '/' . $safeName;

    if (!move_uploaded_file($tmpPath, $target)) {
        http_response_code(500);
        $_SESSION['flash_message'] = '<div style="color:red">Gagal menyimpan file.</div>';
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
    }

    @chmod($target, 0644);

    // Build download URL
    $downloadUrl = safeDownloadUrl('/uploads', array_merge(explode('/', $datePath), [$safeName]));
    $_SESSION['flash_message'] = '<div style="color:green">Upload berhasil! '
        . '<a href="' . htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank">Download</a></div>';

    // PRG redirect
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Upload Dokumen</title>
</head>
<body>
  <h1>Upload Dokumen PDF/DOCX</h1>
  <?php if ($message) echo $message; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="file" name="file" required accept=".pdf,.docx">
    <button type="submit">Upload</button>
  </form>

  <?php listUploadedFiles($config['uploads_dir'], '/uploads'); ?>
</body>
</html>
