<!doctype html><meta charset="utf-8">
<h1>Buat Dokumen</h1>

<form id="postForm" method="post" action="/admin/post" enctype="multipart/form-data" novalidate>
  <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">

  <p>
    <label>Judul <br>
      <input id="title" name="title" required style="width:420px" maxlength="255" autocomplete="off">
    </label>
    <small id="titleCount">0/255</small>
  </p>

  <p>
    <label>Slug (otomatis, bisa diubah) <br>
      <input id="slug" name="slug" style="width:420px" maxlength="255" autocomplete="off" placeholder="otomatis dari judul">
    </label>
  </p>

  <p>
    <label>Excerpt (opsional) <br>
      <textarea id="excerpt" name="excerpt" rows="2" style="width:420px" maxlength="500"></textarea>
    </label>
    <small id="excerptCount">0/500</small>
  </p>

  <p>
    <label>Konten (Markdown) <br>
      <textarea id="content_md" name="content_md" rows="12" style="width:620px"></textarea>
    </label>
  </p>

  <p>
    <label>Upload file asli (PDF/DOCX, opsional, maks 5MB):
      <input id="file" type="file" name="file" accept=".pdf,.docx">
    </label>
    <small id="fileHint">(.pdf, .docx — ≤ 5MB)</small>
  </p>

  <button type="submit">Simpan</button>
</form>

<script>
(function () {
  const title   = document.getElementById('title');
  const slug    = document.getElementById('slug');
  const excerpt = document.getElementById('excerpt');
  const file    = document.getElementById('file');
  const titleCount = document.getElementById('titleCount');
  const excerptCount = document.getElementById('excerptCount');
  const form = document.getElementById('postForm');

  const toSlug = (s) => {
    // transliterate basic accents
    const map = {'à':'a','á':'a','â':'a','ä':'a','ã':'a','å':'a','ç':'c','è':'e','é':'e','ê':'e','ë':'e','ì':'i','í':'i','î':'i','ï':'i','ñ':'n','ò':'o','ó':'o','ô':'o','ö':'o','õ':'o','ù':'u','ú':'u','û':'u','ü':'u','ý':'y','ÿ':'y','ß':'ss'};
    s = s.toLowerCase().replace(/[^\u0000-\u007E]/g, c => map[c] || '');
    s = s.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    return s || 'untitled';
  };

  // Auto-generate slug as user types title (unless user has manually edited slug)
  let slugTouched = false;
  slug.addEventListener('input', () => { slugTouched = slug.value.trim().length > 0; });
  title.addEventListener('input', () => {
    titleCount.textContent = `${title.value.length}/255`;
    if (!slugTouched) slug.value = toSlug(title.value);
  });

  excerpt.addEventListener('input', () => {
    excerptCount.textContent = `${excerpt.value.length}/500`;
  });

  // Client-side file checks (size & extension)
  form.addEventListener('submit', (e) => {
    const f = file.files && file.files[0];
    if (f) {
      const okExt = /\.(pdf|docx)$/i.test(f.name);
      const okSize = f.size <= 5 * 1024 * 1024; // 5MB
      if (!okExt) {
        e.preventDefault();
        alert('Tipe file tidak diizinkan. Hanya PDF atau DOCX.');
        return;
      }
      if (!okSize) {
        e.preventDefault();
        alert('Ukuran file maksimal 5MB.');
        return;
      }
    }
    // normalize slug just before submit
    slug.value = toSlug(slug.value || title.value);
  });

  // Initial counters
  titleCount.textContent = `0/255`;
  excerptCount.textContent = `0/500`;
})();
</script>
