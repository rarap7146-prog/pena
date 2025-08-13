<?php
declare(strict_types=1);

class Post
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../db.php';
    }

    public function all(): array
    {
        return $this->pdo->query(
            "SELECT p.*, c.name as category_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ? 
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ? 
             LIMIT 1"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts (
                title, meta_title, slug, excerpt, 
                meta_description, content_md, featured_image, 
                category_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $data['title'],
            $data['meta_title'] ?? $data['title'],
            $data['slug'] ?? $this->createSlug($data['title']),
            $data['excerpt'] ?? null,
            $data['meta_description'] ?? $data['excerpt'] ?? null,
            $data['content_md'],
            $data['featured_image'] ?? null,
            $data['category_id'] ?? null
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE posts SET 
                title = ?, meta_title = ?, slug = ?, 
                excerpt = ?, meta_description = ?, 
                content_md = ?, featured_image = ?, 
                category_id = ?
             WHERE id = ?"
        );
        
        return $stmt->execute([
            $data['title'],
            $data['meta_title'] ?? $data['title'],
            $data['slug'] ?? $this->createSlug($data['title']),
            $data['excerpt'] ?? null,
            $data['meta_description'] ?? $data['excerpt'] ?? null,
            $data['content_md'],
            $data['featured_image'] ?? null,
            $data['category_id'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function createSlug(string $title): string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        $slug = strtolower($slug ?? '');
        $slug = preg_replace('~[^a-z0-9]+~', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $check = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
        $check->execute([$slug]);
        $count = (int)$check->fetchColumn();
        
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        
        return $slug;
    }
}
