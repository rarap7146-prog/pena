<?php
declare(strict_types=1);

class SitemapController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../db.php';
    }

    public function xml(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        
        $siteConfig = require __DIR__ . '/../../config/app.php';
        $baseUrl = $siteConfig['base_url'] ?? 'https://araska.id';
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        echo "  <url>\n";
        echo "    <loc>{$baseUrl}/</loc>\n";
        echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "    <changefreq>daily</changefreq>\n";
        echo "    <priority>1.0</priority>\n";
        echo "  </url>\n";
        
        // Posts
        $stmt = $this->pdo->query("
            SELECT slug, updated_at, created_at 
            FROM posts 
            ORDER BY created_at DESC
        ");
        
        while ($post = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lastmod = $post['updated_at'] ?: $post['created_at'];
            echo "  <url>\n";
            echo "    <loc>{$baseUrl}/post/{$post['slug']}</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($lastmod)) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }
        
        // Categories
        $stmt = $this->pdo->query("
            SELECT slug 
            FROM categories 
            WHERE slug IS NOT NULL AND slug != ''
            ORDER BY name
        ");
        
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  <url>\n";
            echo "    <loc>{$baseUrl}/category/{$category['slug']}</loc>\n";
            echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.6</priority>\n";
            echo "  </url>\n";
        }
        
        // Categories index
        echo "  <url>\n";
        echo "    <loc>{$baseUrl}/categories</loc>\n";
        echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
        
        echo '</urlset>';
        exit;
    }
}
