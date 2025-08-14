<?php
// Apply content scheduling migration
try {
    $pdo = new PDO('sqlite:app/db.sqlite');
    
    // Add new columns
    $pdo->exec("ALTER TABLE posts ADD COLUMN status VARCHAR(20) DEFAULT 'published'");
    echo "Added status column\n";
    
    $pdo->exec("ALTER TABLE posts ADD COLUMN scheduled_at DATETIME NULL");
    echo "Added scheduled_at column\n";
    
    $pdo->exec("ALTER TABLE posts ADD COLUMN published_at DATETIME NULL");
    echo "Added published_at column\n";
    
    // Update existing posts
    $pdo->exec("UPDATE posts SET status = 'published', published_at = created_at WHERE published_at IS NULL");
    echo "Updated existing posts\n";
    
    // Create indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_posts_status_scheduled ON posts(status, scheduled_at)");
    echo "Created status index\n";
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_posts_published_at ON posts(published_at DESC)");
    echo "Created published_at index\n";
    
    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
?>
