-- Content Scheduling Migration
-- Add scheduling fields to posts table

ALTER TABLE posts ADD COLUMN status VARCHAR(20) DEFAULT 'published';
ALTER TABLE posts ADD COLUMN scheduled_at DATETIME NULL;
ALTER TABLE posts ADD COLUMN published_at DATETIME NULL;

-- Update existing posts to have published status and published_at timestamp
UPDATE posts SET status = 'published', published_at = created_at WHERE status IS NULL;

-- Create index for scheduled posts queries
CREATE INDEX idx_posts_status_scheduled ON posts(status, scheduled_at);
CREATE INDEX idx_posts_published_at ON posts(published_at DESC);
