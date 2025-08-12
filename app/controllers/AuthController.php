<?php
declare(strict_types=1);

class AuthController
{
    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private static function ensureCsrf(): string
    {
        self::ensureSession();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    /** Guard dipanggil dari AdminController */
    public static function requireAdmin(): void
    {
        self::ensureSession();
        if (empty($_SESSION['is_admin'])) {
            header('Location: /login');
            exit;
        }
    }

    /** GET /login */
    public function loginForm(): void
    {
        $csrf = self::ensureCsrf();
        echo <<<HTML
<!doctype html><meta charset="utf-8">
<title>Login Admin</title>
<h1>Login Admin</h1>
<form method="post" action="/login">
  <input type="hidden" name="csrf" value="{$csrf}">
  <p>Password: <input type="password" name="password" required autofocus></p>
  <button type="submit">Masuk</button>
</form>
HTML;
        exit;
    }

    /** POST /login */
    public function login(): void
    {
        self::ensureSession();

        // CSRF check
        $posted = (string)($_POST['csrf'] ?? '');
        if (!hash_equals($_SESSION['csrf'] ?? '', $posted)) {
            http_response_code(403);
            exit('Sesi kedaluwarsa. Silakan muat ulang halaman.');
        }

        // Ambil hash dari config
        $config = require __DIR__ . '/../../config/app.php';
        $hash   = (string)($config['auth']['admin_password_hash'] ?? '');

        if ($hash === '') {
            http_response_code(500);
            exit('Konfigurasi password admin belum diset.');
        }

        $input = (string)($_POST['password'] ?? '');
        $ok    = password_verify($input, $hash);

        if ($ok) {
            $_SESSION['is_admin'] = true;
            header('Location: /admin');
            exit;
        }

        echo '<p style="color:red">Password salah.</p>';
        $this->loginForm();
    }

    /** POST /logout */
    public function logout(): void
    {
        self::ensureSession();
        unset($_SESSION['is_admin']);
        header('Location: /login');
        exit;
    }
}
