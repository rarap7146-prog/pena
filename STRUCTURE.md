# Araska.id - Structure Documentation

## Project Structure

```
araska.id/
├── app/
│   ├── controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── DownloadController.php
│   │   ├── ExportController.php
│   │   └── PostController.php
│   ├── helpers/
│   │   └── site.php
│   ├── models/
│   │   ├── Category.php
│   │   └── Post.php
│   └── db.php
├── config/
│   ├── app.php
│   └── site.json
├── migrations/
│   └── 001_add_seo_and_categories.sql
├── public/
│   ├── css/
│   │   └── style.css
│   ├── uploads/
│   ├── download.php
│   ├── index.php
│   └── upload-image.php
├── storage/
│   ├── cache/
│   ├── sessions/
│   └── uploads/
├── views/
│   ├── admin/
│   │   ├── categories/
│   │   │   ├── index.php      # List all categories
│   │   │   ├── create.php     # Create new category
│   │   │   └── edit.php       # Edit category
│   │   ├── posts/
│   │   │   ├── index.php      # List all posts  
│   │   │   ├── create.php     # Create new post
│   │   │   └── edit.php       # Edit post
│   │   ├── dashboard.php      # Admin dashboard
│   │   └── settings.php       # Site settings
│   ├── categories.php         # Public category list
│   ├── category.php           # Public category view
│   ├── home.php              # Homepage
│   ├── layout.php            # Main layout
│   └── post.php              # Public post view
├── vendor/                   # Composer dependencies
├── composer.json
├── composer.lock
└── .gitignore
```

## Naming Conventions

### Controllers
- **AdminController.php** - Main admin functionality
- **CategoryController.php** - Category CRUD operations
- **PostController.php** - Post CRUD operations
- **AuthController.php** - Authentication & authorization

### Views Structure
- **admin/[resource]/index.php** - List view (e.g., posts/index.php)
- **admin/[resource]/create.php** - Create form (e.g., posts/create.php)
- **admin/[resource]/edit.php** - Edit form (e.g., posts/edit.php)

### Models
- **Category.php** - Category data operations
- **Post.php** - Post data operations

## Removed Files
The following debug/test files were removed for production:
- check_db.php
- check_db2.php  
- check_mysql.php
- debug_edit.php
- test_edit_view.php
- test_favicon.php
- views/admin/new_backup.php
- views/admin/new_redesigned.php

## Routes Updated
- `/admin/posts/new` → uses `views/admin/posts/create.php`
- `/admin/posts` → uses `views/admin/posts/index.php`
- `/admin/posts/edit/{id}` → uses `views/admin/posts/edit.php`
- `/admin/categories/new` → uses `views/admin/categories/create.php`
- `/admin/categories` → uses `views/admin/categories/index.php`
- `/admin/categories/edit/{id}` → uses `views/admin/categories/edit.php`
