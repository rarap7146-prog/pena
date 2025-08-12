<!doctype html><meta charset="utf-8">
<h1><?=htmlspecialchars($post['title'])?></h1>
<?php if(!empty($post['excerpt'])): ?><p><em><?=htmlspecialchars($post['excerpt'])?></em></p><?php endif; ?>
<div>
<?php $Parsedown=new Parsedown(); $Parsedown->setSafeMode(true); echo $Parsedown->text($post['content_md']); ?>
</div>
<h3>Unduh</h3>
<ul>
  <li><a href="/download/pdf?slug=<?=urlencode($post['slug'])?>">Unduh PDF artikel</a></li>
  <li><a href="/download/docx?slug=<?=urlencode($post['slug'])?>">Unduh DOCX artikel</a></li>
  <?php foreach($attachments as $f): ?>
    <li><a href="/download/<?=htmlspecialchars($f['filename'])?>" target="_blank">File lampiran: <?=htmlspecialchars($f['filename'])?></a></li>
  <?php endforeach; ?>
</ul>
