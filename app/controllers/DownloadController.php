<?php
declare(strict_types=1);

class DownloadController
{
    public function file(string $path)
    {
            $config = require __DIR__ . '/../../config/app.php';
            $base   = realpath($config['uploads_dir']);
            $rel    = str_replace(['..', '\\'], '', urldecode($path));
            $full   = realpath($base . '/' . $rel);

            if ($full === false || strpos($full, $base) !== 0 || !is_file($full)) {
                http_response_code(404); exit('File not found.');
            }

            $fname = basename($full);
            header('Content-Type: application/octet-stream'); // force download for anything
            header('Content-Disposition: attachment; filename="' . $fname . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($full));
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            readfile($full);
            exit;
    }
}
