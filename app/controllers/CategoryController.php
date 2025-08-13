<?php
declare(strict_types=1);

require_once __DIR__ . '/AuthController.php';

class CategoryController
{
    private $category;
    
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Category.php';
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

    private function csrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    private function validateCsrf(): void
    {
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
            http_response_code(403);
            exit('CSRF validation failed');
        }
    }

    public function index(): void
    {
        $categories = $this->category->all();
        $csrf = $this->csrf();
        require __DIR__ . '/../../views/admin/categories/index.php';
    }

    public function create(): void
    {
        $csrf = $this->csrf();
        require __DIR__ . '/../../views/admin/categories/create.php';
    }

    public function store(): void
    {
        AuthController::requireAdmin();
        $this->validateCsrf();

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (empty($_POST['name']) || trim($_POST['name']) === '') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Nama kategori harus diisi.']);
            } else {
                $_SESSION['error'] = 'Nama kategori harus diisi.';
                header('Location: /admin/categories/new');
            }
            exit;
        }

        $data = [
            'name' => trim($_POST['name']),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        // Generate slug automatically if empty
        if (empty($data['slug'])) {
            $data['slug'] = $this->category->createSlug($data['name']);
        }

        try {
            $categoryId = $this->category->create($data);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'category' => [
                        'id' => $categoryId,
                        'name' => $data['name'],
                        'slug' => $data['slug']
                    ]
                ]);
            } else {
                $_SESSION['success'] = 'Kategori berhasil dibuat.';
                header('Location: /admin/categories');
            }
        } catch (PDOException $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Gagal membuat kategori: ' . $e->getMessage()]);
            } else {
                $_SESSION['error'] = 'Gagal membuat kategori. ' . $e->getMessage();
                header('Location: /admin/categories/new');
            }
        }
        exit;
    }    public function edit(int $id): void
    {
        $category = $this->category->find($id);
        if (!$category) {
            http_response_code(404);
            exit('Category not found');
        }

        $csrf = $this->csrf();
        require __DIR__ . '/../../views/admin/categories/edit.php';
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        
        if (empty($_POST['name'])) {
            $_SESSION['error'] = 'Nama kategori harus diisi.';
            header("Location: /admin/categories/{$id}/edit");
            exit;
        }

        $data = [
            'name' => trim($_POST['name']),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        try {
            $this->category->update($id, $data);
            $_SESSION['success'] = 'Kategori berhasil diperbarui.';
            header('Location: /admin/categories');
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal memperbarui kategori. ' . $e->getMessage();
            header("Location: /admin/categories/{$id}/edit");
        }
        exit;
    }

    public function delete(int $id): void
    {
        AuthController::requireAdmin();
        $this->validateCsrf();
        
        try {
            $this->category->delete($id);
            $_SESSION['success'] = 'Kategori berhasil dihapus.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menghapus kategori. ' . $e->getMessage();
        }
        
        header('Location: /admin/categories');
        exit;
    }

    public function listAll(): void 
    {
        $stmt = $this->pdo->query('
            SELECT c.*, COUNT(p.id) as post_count 
            FROM categories c 
            LEFT JOIN posts p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY c.name ASC
        ');
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../../views/categories.php';
    }

    public function posts(string $slug): void
    {
        // Get category by slug
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            http_response_code(404);
            exit('Category not found');
        }

        // Get posts in this category
        $stmt = $this->pdo->prepare('
            SELECT title, slug, excerpt, created_at 
            FROM posts 
            WHERE category_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$category['id']]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/category.php';
    }
}
