<!doctype html><meta charset="utf-8">
<h1>Admin</h1>

<form method="post" action="/logout" style="display:inline">
  <button type="submit">Logout</button>
</form>
&nbsp;|&nbsp;
<a href="/admin/post/new">Buat Dokumen</a>

<?php if (empty($posts)): ?>
  <p>Belum ada dokumen.</p>
<?php else: ?>
  <ul>
  <?php foreach ($posts as $p): ?>
    <li>
      <a href="/post/<?=htmlspecialchars($p['slug'])?>"><?=htmlspecialchars($p['title'])?></a>
      <small>(<?=date('Y-m-d', strtotime($p['created_at']))?>)</small>

      <!-- Delete form (POST + CSRF) -->
      <form method="post" action="/admin/post/delete" style="display:inline" onsubmit="return confirm('Hapus dokumen ini? Tindakan tidak bisa dibatalkan.');">
        <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <button type="submit">Hapus</button>
      </form>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
