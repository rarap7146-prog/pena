-- Add new columns to posts table
ALTER TABLE posts
ADD COLUMN meta_title VARCHAR(255) AFTER title,
ADD COLUMN meta_description TEXT AFTER excerpt,
ADD COLUMN featured_image VARCHAR(255) AFTER meta_description,
ADD COLUMN category_id INT AFTER featured_image,
ADD INDEX posts_category_id (category_id);

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add foreign key constraint
ALTER TABLE posts
ADD CONSTRAINT fk_posts_category
FOREIGN KEY (category_id) REFERENCES categories(id)
ON DELETE SET NULL;
