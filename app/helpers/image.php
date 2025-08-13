<?php

/**
 * Image optimization and processing functions
 */

class ImageOptimizer {
    
    private $maxWidth = 600;
    private $maxHeight = 600;
    private $quality = 85; // WebP quality
    private $jpegQuality = 85;
    private $pngCompression = 6; // 0-9, 9 is max compression
    
    public function __construct($maxWidth = 600, $maxHeight = 600, $quality = 85) {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->quality = $quality;
        $this->jpegQuality = $quality;
    }
    
    /**
     * Optimize uploaded image file
     */
    public function optimizeUploadedFile($tmpName, $originalName, $outputDir) {
        // Get image info
        $imageInfo = getimagesize($tmpName);
        if (!$imageInfo) {
            throw new Exception('Invalid image file');
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create image resource
        $sourceImage = $this->createImageFromFile($tmpName, $mimeType);
        if (!$sourceImage) {
            throw new Exception('Could not create image resource');
        }
        
        // Calculate new dimensions
        list($newWidth, $newHeight) = $this->calculateDimensions($originalWidth, $originalHeight);
        
        // Create optimized image
        $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
            imagefill($optimizedImage, 0, 0, $transparent);
        }
        
        // Resize image
        imagecopyresampled(
            $optimizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        
        // Generate filename
        $baseFilename = pathinfo($originalName, PATHINFO_FILENAME);
        $webpFilename = uniqid('img_') . '.webp';
        $fallbackFilename = uniqid('img_') . '.jpg';
        
        // Ensure output directory exists
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $webpPath = $outputDir . '/' . $webpFilename;
        $fallbackPath = $outputDir . '/' . $fallbackFilename;
        
        // Save as WebP (primary)
        $webpSaved = false;
        if (function_exists('imagewebp')) {
            $webpSaved = imagewebp($optimizedImage, $webpPath, $this->quality);
        }
        
        // Save fallback JPEG
        $jpegSaved = imagejpeg($optimizedImage, $fallbackPath, $this->jpegQuality);
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($optimizedImage);
        
        if ($webpSaved) {
            // Delete fallback if WebP is successful
            if (file_exists($fallbackPath)) {
                unlink($fallbackPath);
            }
            return [
                'filename' => $webpFilename,
                'path' => $webpPath,
                'format' => 'webp',
                'size' => filesize($webpPath),
                'dimensions' => [$newWidth, $newHeight]
            ];
        } elseif ($jpegSaved) {
            return [
                'filename' => $fallbackFilename,
                'path' => $fallbackPath,
                'format' => 'jpeg',
                'size' => filesize($fallbackPath),
                'dimensions' => [$newWidth, $newHeight]
            ];
        } else {
            throw new Exception('Failed to save optimized image');
        }
    }
    
    /**
     * Optimize base64 image data
     */
    public function optimizeBase64Image($base64Data, $outputDir) {
        // Parse base64 data
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            throw new Exception('Invalid base64 image data');
        }
        
        $imageType = strtolower($matches[1]);
        $data = substr($base64Data, strpos($base64Data, ',') + 1);
        $data = base64_decode($data);
        
        if ($data === false) {
            throw new Exception('Failed to decode base64 data');
        }
        
        // Create temporary file
        $tmpFile = tempnam(sys_get_temp_dir(), 'img_upload_');
        file_put_contents($tmpFile, $data);
        
        try {
            $result = $this->optimizeUploadedFile($tmpFile, 'clipboard.' . $imageType, $outputDir);
            unlink($tmpFile); // Clean up temp file
            return $result;
        } catch (Exception $e) {
            unlink($tmpFile); // Clean up temp file
            throw $e;
        }
    }
    
    /**
     * Create image resource from file
     */
    private function createImageFromFile($filePath, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    return imagecreatefromwebp($filePath);
                }
                break;
        }
        return false;
    }
    
    /**
     * Calculate new dimensions maintaining aspect ratio
     */
    private function calculateDimensions($originalWidth, $originalHeight) {
        $ratio = min($this->maxWidth / $originalWidth, $this->maxHeight / $originalHeight);
        
        // Don't upscale
        if ($ratio > 1) {
            $ratio = 1;
        }
        
        $newWidth = round($originalWidth * $ratio);
        $newHeight = round($originalHeight * $ratio);
        
        return [$newWidth, $newHeight];
    }
    
    /**
     * Get file size in human readable format
     */
    public function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 1) . ' ' . $units[$pow];
    }
}
