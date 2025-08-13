<?php
// Test script for ImageOptimizer
require_once __DIR__ . '/app/helpers/image.php';

echo "=== ImageOptimizer Test Script ===" . PHP_EOL;
echo "PHP Version: " . PHP_VERSION . PHP_EOL;
echo "GD Extension: " . (extension_loaded('gd') ? 'Loaded' : 'Not loaded') . PHP_EOL;
echo "WebP Support: " . (function_exists('imagewebp') ? 'Available' : 'Not available') . PHP_EOL;
echo PHP_EOL;

// Test directory
$testDir = __DIR__ . '/storage/test-images';
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
    echo "Created test directory: $testDir" . PHP_EOL;
}

// Create a simple test image using GD
$testImagePath = $testDir . '/test-image.jpg';
$width = 1400;
$height = 800;

// Create a test image
$image = imagecreatetruecolor($width, $height);
$backgroundColor = imagecolorallocate($image, 240, 240, 240);
$textColor = imagecolorallocate($image, 50, 50, 50);
imagefill($image, 0, 0, $backgroundColor);

// Add some text
$text = "Test Image {$width}x{$height}";
imagestring($image, 5, 50, 50, $text, $textColor);

// Save the test image
imagejpeg($image, $testImagePath, 100);
imagedestroy($image);

$originalSize = filesize($testImagePath);
echo "Created test image: {$testImagePath}" . PHP_EOL;
echo "Original size: " . number_format($originalSize) . " bytes (" . round($originalSize/1024, 2) . " KB)" . PHP_EOL;
echo "Original dimensions: {$width}x{$height}" . PHP_EOL;
echo PHP_EOL;

// Test ImageOptimizer
echo "Testing ImageOptimizer..." . PHP_EOL;

try {
    $optimizer = new ImageOptimizer();
    
    // Test content image optimization (max 600px width)
    $result = $optimizer->optimizeUploadedFile($testImagePath, basename($testImagePath), $testDir);
    
    echo "✓ Optimization successful!" . PHP_EOL;
    echo "Result data:" . PHP_EOL;
    print_r($result);
    
} catch (Exception $e) {
    echo "✗ Exception caught: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;
echo "Test completed!" . PHP_EOL;
?>
