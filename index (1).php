<?php require 'includes/db.php'; $title = 'Daftar Produk';
$q = trim($_GET['q'] ?? ''); $cat = (int)($_GET['cat'] ?? 0); $like = "%$q%";
$st = $db->prepare("SELECT p.*, c.name cname FROM products p JOIN categories c ON c.id=p.category_id
  WHERE p.name LIKE ? AND (?=0 OR p.category_id=?) ORDER BY p.id DESC");
$st->bind_param('sii', $like, $cat, $cat); $st->execute(); $rows = $st->get_result();
$cats = $db->query("SELECT * FROM categories ORDER BY name");
include 'includes/header.php'; ?>
<form class="row g-2 mb-4" method="get">
  <div class="col-md-6"><input class="form-control" name="q" placeholder="Cari produk..." value="<?= e($q) ?>"></div>
  <div class="col-md-4"><select class="form-select" name="cat"><option value="0">Semua kategori</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select></div>
  <div class="col-md-2 d-grid"><button class="btn btn-primary">Cari</button></div>
</form>
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
<?php foreach ($rows as $p): ?>
  <div class="col"><div class="card h-100 shadow-sm">
    <img src="<?= e($p['image_url']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
    <div class="card-body d-flex flex-column">
      <span class="badge bg-secondary align-self-start mb-1"><?= e($p['cname']) ?></span>
      <h6 class="card-title"><?= e($p['name']) ?></h6>
      <p class="fw-bold text-primary mb-1"><?= rp($p['price']) ?></p>
      <small class="text-muted mb-2">Stok: <?= (int)$p['stock'] ?></small>
      <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto">Lihat Detail</a>
    </div></div></div>
<?php endforeach; if ($rows->num_rows === 0) echo '<p class="text-muted">Produk tidak ditemukan.</p>'; ?>
</div>
<?php include 'includes/footer.php'; ?>