<?php
class PostController {
  public function index() {
    $pdo = require __DIR__.'/../db.php';
    $posts = $pdo->query("
      SELECT p.id, p.title, p.slug, p.excerpt, p.created_at,
             c.name as category_name, c.slug as category_slug 
      FROM posts p 
      LEFT JOIN categories c ON p.category_id = c.id 
      ORDER BY p.created_at DESC
    ")->fetchAll();
    $this->view('home', compact('posts'));
  }
  public function show(string $slug) {
    $pdo = require __DIR__.'/../db.php';
    $st = $pdo->prepare(
        "SELECT p.*, c.name as category_name, c.slug as category_slug 
         FROM posts p 
         LEFT JOIN categories c ON p.category_id = c.id 
         WHERE p.slug = ? 
         LIMIT 1"
    ); 
    $st->execute([$slug]);
    $post = $st->fetch(); 
    if(!$post){ 
        require_once __DIR__ . '/../helpers/errors.php';
        show404();
    }

    $fs = $pdo->prepare("SELECT filename,mime_type,kind FROM attachments WHERE post_id=?");
    $fs->execute([$post['id']]); 
    $attachments = $fs->fetchAll();

    $this->view('post', compact('post','attachments'));
  }
  private function view($tpl,$data=[]){ extract($data,EXTR_SKIP); require __DIR__."/../../views/{$tpl}.php"; }
}