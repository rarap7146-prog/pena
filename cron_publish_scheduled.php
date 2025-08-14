<?php
/**
 * Cron job script to automatically publish scheduled posts
 * Run this script every minute: * * * * * /usr/bin/php /var/www/araska.id/cron_publish_scheduled.php
 */

require_once __DIR__ . '/app/models/Post.php';

try {
    $post = new Post();
    
    // Get scheduled posts that are ready to be published
    $readyPosts = $post->getScheduledPostsReady();
    
    if (count($readyPosts) > 0) {
        // Publish them
        $publishedCount = $post->publishScheduledPosts();
        
        // Log the result
        $logMessage = date('Y-m-d H:i:s') . " - Published {$publishedCount} scheduled posts\n";
        file_put_contents(__DIR__ . '/storage/cron.log', $logMessage, FILE_APPEND | LOCK_EX);
        
        echo "Published {$publishedCount} scheduled posts\n";
    } else {
        echo "No posts ready for publishing\n";
    }
    
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " - Error publishing scheduled posts: " . $e->getMessage() . "\n";
    file_put_contents(__DIR__ . '/storage/cron.log', $errorMessage, FILE_APPEND | LOCK_EX);
    
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
