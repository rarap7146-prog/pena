<?php
declare(strict_types=1);

class Category
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../db.php';
    }

    public function all(): array
    {
        return $this->pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['slug'] ?? $this->createSlug($data['name']),
            $data['description'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['slug'] ?? $this->createSlug($data['name']),
            $data['description'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createSlug(string $name): string
    {
        // Trim whitespace first
        $name = trim($name);
        
        // Handle empty or whitespace-only names
        if ($name === '') {
            return 'category-' . time();
        }
        
        // Convert to ASCII and create slug
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        if ($slug === false) {
            // Fallback if iconv fails
            $slug = $name;
        }
        
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special chars but keep spaces and hyphens
        $slug = preg_replace('/[\s-]+/', '-', $slug);       // Replace spaces and multiple hyphens with single hyphen
        $slug = trim($slug, '-');                           // Remove leading/trailing hyphens
        
        // Handle empty slug after processing
        if ($slug === '') {
            $slug = 'category-' . time();
        }
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            $count = (int)$check->fetchColumn();
            
            if ($count === 0) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
