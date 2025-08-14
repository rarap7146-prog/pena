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
            http_response_code(404);
            exit('Kategori tidak ditemukan');
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
