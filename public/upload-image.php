<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/helpers/image.php';

// Pastikan user sudah login sebagai admin
AuthController::requireAdmin();

// Validasi CSRF
if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'CSRF token mismatch']));
}

$response = ['success' => false];

try {
    $optimizer = new ImageOptimizer(600, 600, 85); // max 600px width/height, 85% quality
    $uploadDir = __DIR__ . '/uploads/content';
    
    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        
        // Validate mime type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedMimes)) {
            http_response_code(400);
            exit(json_encode(['error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP']));
        }
        
        // Check file size (max 10MB before processing)
        if ($file['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            exit(json_encode(['error' => 'File too large. Maximum 10MB allowed.']));
        }
        
        // Optimize image
        $result = $optimizer->optimizeUploadedFile($file['tmp_name'], $file['name'], $uploadDir);
        
        $response = [
            'success' => true,
            'filename' => $result['filename'],
            'url' => '/uploads/content/' . $result['filename'],
            'format' => $result['format'],
            'size' => $optimizer->formatFileSize($result['size']),
            'dimensions' => $result['dimensions'][0] . 'x' . $result['dimensions'][1]
        ];
        
    } elseif (isset($_POST['image'])) {
        // Handle clipboard paste (base64 image data)
        $data = $_POST['image'];
        
        // Optimize base64 image
        $result = $optimizer->optimizeBase64Image($data, $uploadDir);
        
        $response = [
            'success' => true,
            'filename' => $result['filename'],
            'url' => '/uploads/content/' . $result['filename'],
            'format' => $result['format'],
            'size' => $optimizer->formatFileSize($result['size']),
            'dimensions' => $result['dimensions'][0] . 'x' . $result['dimensions'][1]
        ];
        
    } else {
        http_response_code(400);
        $response = ['error' => 'No image data received'];
    }
    
} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => 'Image processing failed: ' . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
