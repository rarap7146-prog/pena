<?php
declare(strict_types=1);


session_start();
require_once __DIR__ . '/../app/controllers/AuthController.php';
// Pastikan user sudah login sebagai admin
AuthController::requireAdmin();
// Validasi CSRF
if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'CSRF token mismatch']));
}

$response = ['success' => false];

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
        exit(json_encode(['error' => 'Invalid file type']));
    }
    
    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_') . '.' . $ext;
    $uploadDir = __DIR__ . '/uploads/content';
    
    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $destination = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $response = [
            'success' => true,
            'filename' => $filename,
            'url' => '/uploads/content/' . $filename
        ];
    } else {
        http_response_code(500);
        $response = ['error' => 'Failed to save file'];
    }
} elseif (isset($_POST['image'])) {
    // Handle clipboard paste (base64 image data)
    $data = $_POST['image'];
    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif
        
        if (!in_array($type, ['jpeg', 'jpg', 'png', 'gif'])) {
            http_response_code(400);
            exit(json_encode(['error' => 'Invalid image type']));
        }
        
        $data = base64_decode($data);
        
        if ($data === false) {
            http_response_code(400);
            exit(json_encode(['error' => 'Invalid image data']));
        }
        
        $filename = uniqid('img_') . '.' . $type;
        $uploadDir = __DIR__ . '/../src/uploads/content';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (file_put_contents($uploadDir . '/' . $filename, $data)) {
            $response = [
                'success' => true,
                'filename' => $filename,
                'url' => '/uploads/content/' . $filename
            ];
        } else {
            http_response_code(500);
            $response = ['error' => 'Failed to save image'];
        }
    } else {
        http_response_code(400);
        $response = ['error' => 'Invalid image data format'];
    }
} else {
    http_response_code(400);
    $response = ['error' => 'No image data received'];
}

header('Content-Type: application/json');
echo json_encode($response);
