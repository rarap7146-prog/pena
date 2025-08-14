<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
  public function index() {
    $post = new Post();
    $posts = $post->all(); // This now filters for published and ready scheduled posts
    $this->view('home', compact('posts'));
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