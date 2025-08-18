<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
  public function index($page = 1) {
    $post = new Post();
    $perPage = 10;
    $page = max(1, (int)$page);
    $offset = ($page - 1) * $perPage;
    $posts = $post->getPaginated($perPage, $offset);
    $totalPosts = $post->getTotalCount();
    $totalPages = (int)ceil($totalPosts / $perPage);

    // **PERUBAHAN DIMULAI DI SINI**
    // Definisikan base URL Anda. Ganti jika perlu.
    $baseUrl = 'https://araska.id'; 

    // Always set canonical, prev, next, even if only one page
    $canonicalUrl = $page > 1 ? $baseUrl . '/page/' . $page : $baseUrl . '/';
    $prevUrl = $page > 1 ? ($page === 2 ? $baseUrl . '/' : $baseUrl . '/page/' . ($page - 1)) : null;
    $nextUrl = $page < $totalPages ? $baseUrl . '/page/' . ($page + 1) : null;
    $pagination = [
      'current' => $page,
      'total' => $totalPages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $totalPages,
      'canonicalUrl' => $canonicalUrl,
      'prevUrl' => $prevUrl,
      'nextUrl' => $nextUrl,
    ];
    // **PERUBAHAN SELESAI**
    $this->view('home', compact('posts', 'pagination'));
  }
  
  public function show(string $slug) {
    $post = new Post();
    $postData = $post->findBySlug($slug); // This now filters for published and ready scheduled posts
    
    if(!$postData){ 
        require_once __DIR__ . '/../helpers/errors.php';
        show404();
    }

    $pdo = require __DIR__.'/../db.php';
    $fs = $pdo->prepare("SELECT filename,mime_type,kind FROM attachments WHERE post_id=?");
    $fs->execute([$postData['id']]); 
    $attachments = $fs->fetchAll();

    $this->view('post', ['post' => $postData, 'attachments' => $attachments]);
  }
  
  private function view($tpl,$data=[]){ extract($data,EXTR_SKIP); require __DIR__."/../../views/{$tpl}.php"; }
}