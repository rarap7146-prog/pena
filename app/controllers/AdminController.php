<?php
declare(strict_types=1);

require_once __DIR__ . '/AuthController.php';

class AdminController
{
  /* ---------- helpers ---------- */

  private function ensureSession(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  private function csrf() {
    $this->ensureSession();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $_SESSION['csrf'] = bin2hex(random_bytes(16));
      return $_SESSION['csrf'];
    }
    $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
    if (!$ok) { http_response_code(403); exit('CSRF'); }
  }

  private function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text ?? '');
    $text = preg_replace('~[^a-z0-9]+~', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'untitled';
  }

  private function uniqueSlug(string $baseSlug, \PDO $pdo): string {
    $slug = $baseSlug;
    $i = 1;
    $check = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
    while (true) {
      $check->execute([$slug]);
      if ((int)$check->fetchColumn() === 0) return $slug;
      $i++;
      $slug = $baseSlug . '-' . $i; // lorem -> lorem-2 -> lorem-3 ...
    }
  }

  private function view(string $tpl, array $data = []): void {
    extract($data, EXTR_SKIP);
    require __DIR__ . "/../../views/{$tpl}.php";
  }

  /* ---------- actions ---------- */

  public function dashboard(): void {
    AuthController::requireAdmin();
    $pdo = require __DIR__ . '/../db.php';
    $posts = $pdo->query(
      "SELECT id,title,slug,created_at FROM posts ORDER BY created_at DESC"
    )->fetchAll();
    $csrf = $this->csrf();
    $this->view('admin/dashboard', compact('posts','csrf'));
  }

  public function createForm(): void {
    AuthController::requireAdmin();
    $csrf = $this->csrf();
    $this->view('admin/new', compact('csrf'));
  }

  public function store(): void {
    AuthController::requireAdmin();
    $this->csrf();

    $pdo = require __DIR__ . '/../db.php';

    $title   = trim($_POST['title']    ?? '');
    $slugIn  = trim($_POST['slug']     ?? '');
    $excerpt = trim($_POST['excerpt']  ?? '');
    $content = trim($_POST['content_md'] ?? '');

    // Slug base and uniqueness
    $baseSlug = $slugIn !== '' ? $this->slugify($slugIn) : $this->slugify($title);
    $slug     = $this->uniqueSlug($baseSlug, $pdo);

    // Insert post
    $stmt = $pdo->prepare(
      "INSERT INTO posts (title,slug,excerpt,content_md,created_at)
       VALUES (?,?,?,?,NOW())"
    );
    $stmt->execute([$title,$slug,$excerpt,$content]);
    $postId = (int)$pdo->lastInsertId();

    // Optional file upload with debugging
    if (!empty($_FILES['file']['name'])) {
        error_log("=== FILE UPLOAD DEBUG START ===");
        error_log("Original filename: " . $_FILES['file']['name']);
        error_log("Tmp path: " . ($_FILES['file']['tmp_name'] ?? '[none]'));
        error_log("Error code: " . ($_FILES['file']['error'] ?? '[none]'));

        $tmp = $_FILES['file']['tmp_name'] ?? '';

        if ($tmp && is_uploaded_file($tmp)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($tmp) : '[no mime]';
            $ext  = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            error_log("Detected MIME: {$mime}");
            error_log("Detected EXT: {$ext}");

            $allowedPdfMimes  = ['application/pdf', 'application/octet-stream', 'binary/octet-stream', 'application/x-pdf'];
            $allowedDocxMimes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];

            $isPdf  = ($ext === 'pdf')  && in_array($mime, $allowedPdfMimes, true);
            $isDocx = ($ext === 'docx') && in_array($mime, $allowedDocxMimes, true);

            if ($isPdf || $isDocx) {
                $cfg = require __DIR__ . '/../../config/app.php';
                $uploadsDir = rtrim($cfg['uploads_dir'] ?? '', '/');
                error_log("Uploads dir: {$uploadsDir}");

                if (!is_dir($uploadsDir)) {
                    $mkdirOk = @mkdir($uploadsDir, 0755, true);
                    error_log("Creating uploads dir: " . ($mkdirOk ? "OK" : "FAILED"));
                }

                $safeName = $postId . '_' . time() . '_' .
                            preg_replace('~[^a-zA-Z0-9.\-_]~','_', $_FILES['file']['name']);
                $target = $uploadsDir . '/' . $safeName;
                error_log("Target path: {$target}");

                if (!is_writable($uploadsDir)) {
                    error_log("ERROR: Upload dir not writable.");
                } elseif (!move_uploaded_file($tmp, $target)) {
                    error_log("ERROR: move_uploaded_file failed.");
                } else {
                    @chmod($target, 0644);
                    $pdo->prepare(
                        "INSERT INTO attachments (post_id,filename,mime_type,kind,created_at)
                        VALUES (?,?,?,?,NOW())"
                    )->execute([$postId, $safeName, $mime, 'source']);
                    error_log("File uploaded & DB record inserted.");
                }
            } else {
                error_log("ERROR: File rejected by MIME/EXT filter. MIME={$mime}, EXT={$ext}");
            }
        } else {
            error_log("ERROR: No valid uploaded tmp file found.");
        }

        error_log("=== FILE UPLOAD DEBUG END ===");
    }



    header("Location: /post/{$slug}");
    exit;
  }

  public function delete(): void {
    AuthController::requireAdmin();
    $this->csrf();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      exit('Method Not Allowed');
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
      http_response_code(400);
      exit('Invalid ID');
    }

    $pdo = require __DIR__ . '/../db.php';

    $chk = $pdo->prepare('SELECT id FROM posts WHERE id = ?');
    $chk->execute([$id]);
    if (!$chk->fetch()) {
      http_response_code(404);
      exit('Post not found');
    }

    $del = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $del->execute([$id]);

    header('Location: /admin');
    exit;
  }
}
