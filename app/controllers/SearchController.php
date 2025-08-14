<?php
declare(strict_types=1);

class SearchController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../db.php';
    }

    /**
     * Handle search requests
     */
    public function search(): void
    {
        $query = trim((string)($_GET['q'] ?? ''));
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 10;
        
        // Redirect to homepage if empty query
        if ($query === '') {
            header('Location: /');
            exit;
        }
        
        // Get search results
        $results = $this->searchPosts($query, $page, $perPage);
        $totalResults = $this->countSearchResults($query);
        $totalPages = (int)ceil($totalResults / $perPage);
        
        // Load site settings
        require_once __DIR__ . '/../helpers/site.php';
        $siteSettings = getSiteSettings();
        
        // Prepare data for view
        $searchData = [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ];
        
        include __DIR__ . '/../../views/search.php';
    }

    /**
     * Search posts by query
     */
    private function searchPosts(string $query, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = '%' . $query . '%';
        
        // Search only published and ready scheduled posts
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE (
                p.title LIKE ? OR 
                p.content_md LIKE ? OR 
                p.excerpt LIKE ?
            )
            AND (p.status = 'published' 
                 OR (p.status = 'scheduled' AND p.scheduled_at <= NOW()))
            ORDER BY p.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total search results
     */
    private function countSearchResults(string $query): int
    {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM posts p
            WHERE (
                p.title LIKE ? OR 
                p.content_md LIKE ? OR 
                p.excerpt LIKE ?
            )
            AND (p.status = 'published' 
                 OR (p.status = 'scheduled' AND p.scheduled_at <= NOW()))
        ");
        
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get search suggestions (for autocomplete)
     */
    public function suggestions(): void
    {
        $query = trim((string)($_GET['q'] ?? ''));
        
        if (strlen($query) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT title
            FROM posts 
            WHERE title LIKE ?
            AND (status = 'published' 
                 OR (status = 'scheduled' AND scheduled_at <= NOW()))
            ORDER BY title
            LIMIT 5
        ");
        
        $stmt->execute([$searchTerm]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        header('Content-Type: application/json');
        echo json_encode($suggestions);
    }
}
