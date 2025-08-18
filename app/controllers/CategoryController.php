<?php
declare(strict_types=1);

require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private $category;
    private $pdo;

    public function __construct()
    {
        $this->category = new Category();
        $this->ensureSession();
        $this->pdo = require __DIR__ . '/../db.php';
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function validateCsrf(): bool
    {
        session_start();
        $posted = (string)($_POST['csrf'] ?? '');
        $session = $_SESSION['csrf'] ?? '';
        
        return hash_equals($session, $posted);
    }

    public function index(): void
    {
        AuthController::requireAdmin();
        $this->ensureSession();
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        $csrf = $_SESSION['csrf'];
        $categories = $this->category->all();
        include __DIR__ . '/../../views/admin/categories/index.php';
    }

    public function listAll(): void
    {
        // Public category listing (no auth required)
        $categories = $this->category->all();
        
        // Add post count for each category (only published and ready scheduled posts)
        foreach ($categories as &$cat) {
            $stmt = $this->pdo->prepare('
                SELECT COUNT(*) FROM posts 
                WHERE category_id = ? 
                AND (status = "published" 
                     OR (status = "scheduled" AND scheduled_at <= NOW()))
            ');
            $stmt->execute([$cat['id']]);
            $cat['post_count'] = (int)$stmt->fetchColumn();
        }
        
        include __DIR__ . '/../../views/categories.php';
    }

    public function posts(string $slug, int $page = 1): void
    {
        // Get category by slug
        $category = $this->category->findBySlug($slug);
        if (!$category) {
            require_once __DIR__ . '/../helpers/errors.php';
            show404();
        }

        // Pagination settings
        $perPage = 10;
        // Allow page override from query param if provided
        if (!empty($_GET['page']) && is_numeric($_GET['page'])) {
            $page = max(1, (int)$_GET['page']);
        }
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $perPage;

        // Get total count for this category (only published and ready scheduled posts)
        $countStmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM posts p
            WHERE p.category_id = ?
            AND (p.status = "published" OR (p.status = "scheduled" AND p.scheduled_at <= NOW()))
        ');
        $countStmt->execute([$category['id']]);
        $total = (int)$countStmt->fetchColumn();
        $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;

        // Get posts in this category (paginated)
        // Use named parameters only (mixing named and positional is not supported)
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.category_id = :category_id 
             AND (p.status = "published" OR (p.status = "scheduled" AND p.scheduled_at <= NOW()))
             ORDER BY p.created_at DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':category_id', $category['id'], \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare pagination URLs
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = $config['base_url'] ?? 'https://araska.id';
        $canonicalBase = rtrim($baseUrl, '/') . '/category/' . $category['slug'];
        // Pretty URLs: /category/{slug}/page/{n}
        $canonicalUrl = $page > 1 ? $canonicalBase . '/page/' . $page : $canonicalBase;
        $prevUrl = null;
        if ($page > 1) {
            $prevUrl = $page === 2 ? $canonicalBase : $canonicalBase . '/page/' . ($page - 1);
        }
        $nextUrl = $page < $totalPages ? $canonicalBase . '/page/' . ($page + 1) : null;

        $pagination = [
            'current' => $page,
            'total' => $totalPages,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $totalPages,
            'canonicalUrl' => $canonicalUrl,
            'prevUrl' => $prevUrl,
            'nextUrl' => $nextUrl,
        ];

        include __DIR__ . '/../../views/category.php';
    }

    public function create(): void
    {
        AuthController::requireAdmin();
        
        // Generate CSRF token
        $this->ensureSession();
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        $csrf = $_SESSION['csrf'];
        
        include __DIR__ . '/../../views/admin/categories/create.php';
    }

    public function store(): void
    {
        AuthController::requireAdmin();
        
        if (!$this->validateCsrf()) {
            http_response_code(403);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Token keamanan tidak valid']);
            }
            exit;
        }
        
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        
        if ($name === '') {
            http_response_code(400);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Nama kategori harus diisi']);
            }
            exit;
        }
        
        $category = new Category();
        
        try {
            $categoryId = $category->create([
                'name' => $name,
                'description' => $description
            ]);
            
            // Get created category for response
            $createdCategory = $category->find($categoryId);
            
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'category' => $createdCategory
                ]);
            } else {
                header('Location: /admin/posts');
            }
        } catch (Exception $e) {
            error_log("Category creation error: " . $e->getMessage());
            http_response_code(500);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Gagal membuat kategori: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    public function show(int $id): void
    {
        AuthController::requireAdmin();
        
        $category = $this->category->find($id);
        if (!$category) {
            http_response_code(404);
            exit('Kategori tidak ditemukan');
        }
        
        include __DIR__ . '/../../views/admin/categories/show.php';
    }

    public function edit(int $id): void
    {
        AuthController::requireAdmin();
        
        $category = $this->category->find($id);
        if (!$category) {
            require_once __DIR__ . '/../helpers/errors.php';
            show404();
        }
        
        // Generate CSRF token
        $this->ensureSession();
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        $csrf = $_SESSION['csrf'];
        
        include __DIR__ . '/../../views/admin/categories/edit.php';
    }

    public function update(int $id): void
    {
        AuthController::requireAdmin();
        
        if (!$this->validateCsrf()) {
            http_response_code(403);
            exit('Token keamanan tidak valid');
        }
        
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        
        if ($name === '') {
            http_response_code(400);
            exit('Nama kategori harus diisi');
        }
        
        $result = $this->category->update($id, [
            'name' => $name,
            'description' => $description
        ]);
        
        if ($result) {
            header('Location: /admin/categories');
        } else {
            http_response_code(500);
            exit('Gagal memperbarui kategori');
        }
    }

    public function delete(int $id): void
    {
        AuthController::requireAdmin();
        
        if (!$this->validateCsrf()) {
            http_response_code(403);
            exit('Token keamanan tidak valid');
        }
        
        $result = $this->category->delete($id);
        
        if ($result) {
            header('Location: /admin/categories');
        } else {
            http_response_code(500);
            exit('Gagal menghapus kategori');
        }
    }
}
