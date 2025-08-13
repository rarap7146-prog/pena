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
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
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
    
    // Get posts with category info
    $posts = $pdo->query(
      "SELECT p.id, p.title, p.slug, p.created_at, c.name as category_name
       FROM posts p 
       LEFT JOIN categories c ON p.category_id = c.id 
       ORDER BY p.created_at DESC"
    )->fetchAll();
    
    // Get categories count
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
    
    $csrf = $this->csrf();
    $this->view('admin/dashboard', compact('posts', 'categories', 'csrf'));
  }

  public function createForm(): void {
    AuthController::requireAdmin();
    $csrf = $this->csrf();
    
    require_once __DIR__ . '/../models/Category.php';
    $categoryModel = new Category();
    $categories = $categoryModel->all();
    
    $this->view('admin/posts/create', compact('csrf', 'categories'));
  }

  public function store(): void {
    AuthController::requireAdmin();
    $this->csrf();

    $pdo = require __DIR__ . '/../db.php';

    $title           = trim($_POST['title'] ?? '');
    $metaTitle       = trim($_POST['meta_title'] ?? $title);
    $slugIn          = trim($_POST['slug'] ?? '');
    $excerpt         = trim($_POST['excerpt'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? $excerpt);
    $content         = trim($_POST['content_md'] ?? '');
    $categoryId      = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    // Validation
    if (empty($title)) {
      http_response_code(400);
      exit('Title is required');
    }
    if (empty($content)) {
      http_response_code(400);
      exit('Content is required');
    }

    // Slug base and uniqueness
    $baseSlug = $slugIn !== '' ? $this->slugify($slugIn) : $this->slugify($title);
    $slug     = $this->uniqueSlug($baseSlug, $pdo);

    // Handle featured image upload with optimization
    $featuredImage = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $uploadedFile = $_FILES['featured_image'];
        if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $uploadedFile['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mime, $allowedMimes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/featured-image';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                try {
                    require_once __DIR__ . '/../helpers/image.php';
                    $optimizer = new ImageOptimizer(1200, 630, 90); // Featured image: 1200x630 for social sharing
                    $result = $optimizer->optimizeUploadedFile(
                        $uploadedFile['tmp_name'], 
                        $uploadedFile['name'], 
                        $uploadDir
                    );
                    $featuredImage = '/uploads/featured-image/' . $result['filename'];
                } catch (Exception $e) {
                    // Fallback to original upload method if optimization fails
                    $ext = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
                    $newFilename = uniqid('img_') . '.' . $ext;
                    $destination = $uploadDir . '/' . $newFilename;
                    
                    if (move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
                        $featuredImage = '/uploads/featured-image/' . $newFilename;
                    }
                }
            }
        }
    }

    // Insert post
    $stmt = $pdo->prepare(
      "INSERT INTO posts (
        title, meta_title, slug, excerpt, meta_description, 
        content_md, featured_image, category_id, created_at
       ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([
        $title, $metaTitle, $slug, $excerpt, $metaDescription,
        $content, $featuredImage, $categoryId
    ]);
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

  public function posts(): void {
    AuthController::requireAdmin();
    $pdo = require __DIR__ . '/../db.php';
    
    // Get posts with category info
    $posts = $pdo->query(
      "SELECT p.id, p.title, p.slug, p.created_at, p.excerpt, c.name as category_name
       FROM posts p 
       LEFT JOIN categories c ON p.category_id = c.id 
       ORDER BY p.created_at DESC"
    )->fetchAll();
    
    $csrf = $this->csrf();
    $this->view('admin/posts/index', compact('posts', 'csrf'));
  }

  public function settings(): void {
    AuthController::requireAdmin();
    
    // Get current settings (we'll create a simple config file approach)
    $config = [];
    $configFile = __DIR__ . '/../../config/site.json';
    if (file_exists($configFile)) {
      $config = json_decode(file_get_contents($configFile), true) ?? [];
    }
    
    // Default values
    $settings = array_merge([
      'site_name' => 'Araska.id',
      'site_description' => 'Dokumen dan informasi terkini',
      'site_favicon' => '/favicon.ico'
    ], $config);
    
    $csrf = $this->csrf();
    $this->view('admin/settings', compact('settings', 'csrf'));
  }

  public function updateSettings(): void {
    AuthController::requireAdmin();
    
    if (!$this->csrf()) {
      http_response_code(403);
      exit('CSRF token mismatch');
    }

    $settings = [
      'site_name' => trim($_POST['site_name'] ?? ''),
      'site_description' => trim($_POST['site_description'] ?? ''),
      'site_favicon' => '/favicon.ico', // default
      'home' => [
        'title' => trim($_POST['home_title'] ?? ''),
        'meta_description' => trim($_POST['home_meta_description'] ?? '')
      ]
    ];

    // Handle favicon upload
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
      $uploadedFile = $_FILES['site_favicon'];
      
      // Validate file size (max 2MB)
      if ($uploadedFile['size'] > 2 * 1024 * 1024) {
        http_response_code(400);
        exit('File favicon terlalu besar. Maksimal 2MB.');
      }
      
      // Validate file type
      $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon'];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
      finfo_close($finfo);
      
      if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        exit('Tipe file tidak didukung. Gunakan .ico, .png, .jpg, atau .gif');
      }
      
      // Generate unique filename
      $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
      $filename = 'favicon_' . time() . '.' . $extension;
      $uploadPath = __DIR__ . '/../../public/uploads/favicons/';
      
      // Create directory if not exists
      if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
      }
      
      $fullPath = $uploadPath . $filename;
      
      if (move_uploaded_file($uploadedFile['tmp_name'], $fullPath)) {
        $settings['site_favicon'] = '/uploads/favicons/' . $filename;
      } else {
        http_response_code(500);
        exit('Gagal mengupload favicon.');
      }
    } else {
      // Keep existing favicon if no new upload
      $configFile = __DIR__ . '/../../config/site.json';
      if (file_exists($configFile)) {
        $existingConfig = json_decode(file_get_contents($configFile), true) ?? [];
        $settings['site_favicon'] = $existingConfig['site_favicon'] ?? '/favicon.ico';
      }
    }

    // Load existing settings to preserve analytics data
    $configFile = __DIR__ . '/../../config/site.json';
    $existingSettings = [];
    if (file_exists($configFile)) {
      $existingSettings = json_decode(file_get_contents($configFile), true) ?? [];
    }
    
    // Merge with existing settings to preserve analytics
    $finalSettings = array_merge($existingSettings, $settings);

    if (!file_put_contents($configFile, json_encode($finalSettings, JSON_PRETTY_PRINT))) {
      http_response_code(500);
      exit('Failed to save settings');
    }

    header('Location: /admin/settings?saved=1');
    exit;
  }

  public function analytics(): void {
    AuthController::requireAdmin();
    $this->view('admin/analytics');
  }

  public function updateAnalytics(): void {
    AuthController::requireAdmin();
    
    if (!$this->csrf()) {
      http_response_code(403);
      exit('CSRF token mismatch');
    }

    // Load current settings
    $configFile = __DIR__ . '/../../config/site.json';
    $settings = json_decode(file_get_contents($configFile), true);
    
    // Update analytics settings
    $settings['analytics'] = [
      'ga4_measurement_id' => trim($_POST['ga4_measurement_id'] ?? ''),
      'gtag_config' => [
        'send_page_view' => true,
        'anonymize_ip' => true,
        'cookie_domain' => 'auto',
        'cookie_expires' => 63072000
      ],
      'google_search_console_verification' => trim($_POST['google_search_console_verification'] ?? ''),
      'enable_performance_monitoring' => isset($_POST['enable_performance_monitoring'])
    ];

    // Save settings
    if (!file_put_contents($configFile, json_encode($settings, JSON_PRETTY_PRINT))) {
      http_response_code(500);
      exit('Failed to save analytics settings');
    }

    header('Location: /admin/analytics?saved=1');
    exit;
  }

  public function editPost(int $id = null): void {
    AuthController::requireAdmin();
    
    // Get ID from parameter or GET
    if ($id === null) {
      $id = $_GET['id'] ?? null;
    }
    
    if (!$id) {
      http_response_code(404);
      exit('Post not found');
    }

    $pdo = require __DIR__ . '/../db.php';
    
    // Get post data
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
      http_response_code(404);
      exit('Post not found');
    }

    // Get categories for dropdown
    $stmt = $pdo->prepare('SELECT * FROM categories ORDER BY name');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get attachments for this post
    $stmt = $pdo->prepare('SELECT filename, mime_type, kind FROM attachments WHERE post_id = ?');
    $stmt->execute([$id]);
    $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $csrf = $this->csrf();
    $this->view('admin/posts/edit', compact('post', 'categories', 'attachments', 'csrf'));
  }

  public function updatePost(int $id = null): void {
    AuthController::requireAdmin();
    $this->csrf();

    // Get ID from parameter or POST
    if ($id === null) {
      $id = $_POST['id'] ?? null;
    }
    
    if (!$id) {
      http_response_code(404);
      exit('Post not found');
    }

    $pdo = require __DIR__ . '/../db.php';
    
    // Check if post exists
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $existingPost = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingPost) {
      http_response_code(404);
      exit('Post not found');
    }

    $title = trim($_POST['title'] ?? '');
    $content_md = trim($_POST['content_md'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');

    // Validate required fields
    if (empty($title) || empty($content_md)) {
      http_response_code(400);
      exit('Title and content are required');
    }

    // Generate slug from title
    $slug = $this->slugify($title);

    // Check if slug already exists (excluding current post)
    $stmt = $pdo->prepare('SELECT id FROM posts WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
      $slug .= '-' . time();
    }

    // Handle featured image upload with optimization
    $featured_image = $existingPost['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
      $uploadedFile = $_FILES['featured_image'];
      
      // Validate image with finfo for better accuracy
      $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $uploadedFile['tmp_name']);
      finfo_close($finfo);
      
      if (!in_array($mime, $allowedMimes)) {
        http_response_code(400);
        exit('Invalid image type');
      }

      if ($uploadedFile['size'] > 10 * 1024 * 1024) { // 10MB before processing
        http_response_code(400);
        exit('Image too large');
      }

      // Create upload directory
      $uploadDir = __DIR__ . '/../../public/uploads/featured-image';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      try {
        require_once __DIR__ . '/../helpers/image.php';
        $optimizer = new ImageOptimizer(1200, 630, 90); // Featured image optimized for social sharing
        $result = $optimizer->optimizeUploadedFile(
          $uploadedFile['tmp_name'], 
          $uploadedFile['name'], 
          $uploadDir
        );
        
        // Delete old image if exists
        if ($featured_image && file_exists(__DIR__ . '/../../public' . $featured_image)) {
          unlink(__DIR__ . '/../../public' . $featured_image);
        }
        
        $featured_image = '/uploads/featured-image/' . $result['filename'];
        
      } catch (Exception $e) {
        // Fallback to original method if optimization fails
        $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $filename = 'img_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
          // Delete old image if exists
          if ($featured_image && file_exists(__DIR__ . '/../../public' . $featured_image)) {
            unlink(__DIR__ . '/../../public' . $featured_image);
          }
          $featured_image = '/uploads/featured-image/' . $filename;
        }
      }
    }

    // Set default meta values
    if (empty($meta_title)) {
      $meta_title = $title;
    }
    if (empty($meta_description)) {
      $meta_description = $excerpt;
    }

    // Update post
    $stmt = $pdo->prepare('
      UPDATE posts 
      SET title = ?, slug = ?, content_md = ?, excerpt = ?, category_id = ?, 
          featured_image = ?, meta_title = ?, meta_description = ?, updated_at = CURRENT_TIMESTAMP
      WHERE id = ?
    ');

    if ($stmt->execute([
      $title, $slug, $content_md, $excerpt, $category_id,
      $featured_image, $meta_title, $meta_description, $id
    ])) {
      $_SESSION['success'] = 'Post berhasil diperbarui';
      header('Location: /admin/posts');
    } else {
      http_response_code(500);
      exit('Failed to update post');
    }
    exit;
  }
}
