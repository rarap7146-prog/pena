<?php

class Post
{
    /**
     * @var \PDO Objek koneksi database PDO.
     */
    private $pdo;

    /**
     * Constructor untuk kelas Post.
     * Menginisialisasi koneksi database.
     */
    public function __construct()
    {
        // Memuat file konfigurasi database dan menyimpan koneksi PDO.
        $this->pdo = require __DIR__ . '/../db.php';
    }

    /**
     * Mengambil postingan untuk halaman utama dengan paginasi.
     * Hanya mengambil postingan yang 'published' atau 'scheduled' yang waktunya sudah lewat.
     * @param int $limit Jumlah postingan per halaman.
     * @param int $offset Jumlah postingan yang dilewati (untuk paginasi).
     * @return array Daftar postingan.
     */
    public function getPaginated(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'published' 
                OR (p.status = 'scheduled' AND p.scheduled_at <= NOW())
             ORDER BY p.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung total postingan yang akan ditampilkan di halaman utama (untuk paginasi).
     * @return int Jumlah total postingan.
     */
    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM posts 
             WHERE status = 'published' 
                OR (status = 'scheduled' AND scheduled_at <= NOW())"
        );
        return (int)$stmt->fetchColumn();
    }

    /**
     * Mengambil semua postingan yang bisa dilihat publik.
     * @return array Daftar semua postingan publik.
     */
    public function all(): array
    {
        return $this->pdo->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'published' 
                OR (p.status = 'scheduled' AND p.scheduled_at <= NOW())
             ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    /**
     * Mengambil semua postingan untuk admin (termasuk draft dan scheduled).
     * @return array Daftar semua postingan.
     */
    public function allForAdmin(): array
    {
        return $this->pdo->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    /**
     * Mencari satu postingan berdasarkan ID.
     * @param int $id ID dari postingan.
     * @return array|null Data postingan atau null jika tidak ditemukan.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ? 
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Mencari satu postingan berdasarkan slug-nya.
     * Hanya mencari postingan yang sudah bisa dilihat publik.
     * @param string $slug Slug dari postingan.
     * @return array|null Data postingan atau null jika tidak ditemukan.
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM posts p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ? 
               AND (p.status = 'published' OR (p.status = 'scheduled' AND p.scheduled_at <= NOW()))
             LIMIT 1"
        );
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Membuat postingan baru di database.
     * @param array $data Data untuk postingan baru.
     * @return int ID dari postingan yang baru dibuat.
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts (
                title, meta_title, slug, excerpt, 
                meta_description, content_md, featured_image, 
                category_id, status, scheduled_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $data['title'],
            $data['meta_title'] ?? $data['title'],
            $data['slug'] ?? $this->createSlug($data['title']),
            $data['excerpt'] ?? null,
            $data['meta_description'] ?? $data['excerpt'] ?? null,
            $data['content_md'],
            $data['featured_image'] ?? null,
            $data['category_id'] ?? null,
            $data['status'] ?? 'published',
            $data['scheduled_at'] ?? null
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Memperbarui data postingan yang sudah ada.
     * @param int $id ID postingan yang akan diupdate.
     * @param array $data Data baru untuk postingan.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE posts SET 
                title = ?, meta_title = ?, slug = ?, 
                excerpt = ?, meta_description = ?, 
                content_md = ?, featured_image = ?, 
                category_id = ?, status = ?, scheduled_at = ?,
                updated_at = NOW()
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
            $data['status'] ?? 'published',
            $data['scheduled_at'] ?? null,
            $id
        ]);
    }

    /**
     * Menghapus postingan dari database.
     * @param int $id ID postingan yang akan dihapus.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Mempublikasikan postingan yang statusnya 'scheduled' dan waktunya sudah tiba.
     * @return int Jumlah baris yang terpengaruh (jumlah postingan yang dipublikasikan).
     */
    public function publishScheduledPosts(): int
    {
        $stmt = $this->pdo->prepare(
            "UPDATE posts 
             SET status = 'published' 
             WHERE status = 'scheduled' 
               AND scheduled_at <= NOW()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    /**
     * Membuat slug yang unik dari judul postingan.
     * @param string $title Judul postingan.
     * @return string Slug yang dihasilkan.
     */
    private function createSlug(string $title): string
    {
        // 1. Ubah ke huruf kecil dan hapus karakter non-ASCII
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        $slug = strtolower($slug ?? '');
        
        // 2. Ganti karakter non-alfanumerik dengan strip (-)
        $slug = preg_replace('~[^a-z0-9]+~', '-', $slug);
        
        // 3. Hapus strip di awal dan akhir
        $slug = trim($slug, '-');
        
        // 4. Cek apakah slug sudah ada, jika iya, tambahkan angka unik
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
            $check->execute([$slug]);
            $count = (int)$check->fetchColumn();

            if ($count === 0) {
                break; // Slug unik, keluar dari loop
            }
            
            // Jika sudah ada, tambahkan angka di belakangnya
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}