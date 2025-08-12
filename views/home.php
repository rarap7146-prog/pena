<!doctype html><meta charset="utf-8">
<h1>Daftar Dokumen</h1>
<p><a href="/admin">Admin</a></p>
<ul>
<?php foreach($posts as $p): ?>
  <li>
    <a href="/post/<?=htmlspecialchars($p['slug'])?>"><?=htmlspecialchars($p['title'])?></a>
    <small>(<?=date('Y-m-d', strtotime($p['created_at']))?>)</small>
  </li>
<?php endforeach; ?>
</ul>
