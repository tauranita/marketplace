<?php require 'includes/db.php';
$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare("SELECT p.*, c.name cname, u.name seller FROM products p JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.seller_id WHERE p.id=?");
$st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc();
if (!$p) { http_response_code(404); die('Produk tidak ditemukan.'); }
$title = $p['name']; include 'includes/header.php'; ?>
<div class="row g-4">
  <div class="col-md-5"><img src="<?= e($p['image_url']) ?>" class="img-fluid rounded shadow-sm" alt=""></div>
  <div class="col-md-7">
    <span class="badge bg-secondary"><?= e($p['cname']) ?></span>
    <h3 class="mt-2"><?= e($p['name']) ?></h3>
    <h4 class="text-primary"><?= rp($p['price']) ?></h4>
    <p class="text-muted mb-1">Penjual: <?= e($p['seller']) ?> | Stok: <?= (int)$p['stock'] ?></p>
    <p><?= nl2br(e($p['description'])) ?></p>
    <?php if ($p['stock'] > 0): ?>
    <form method="post" action="cart.php" class="d-flex gap-2" style="max-width:300px">
      <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= $p['id'] ?>">
      <input type="number" name="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>" class="form-control">
      <button class="btn btn-primary text-nowrap">+ Keranjang</button>
    </form>
    <?php else: ?><span class="badge bg-danger">Stok habis</span><?php endif; ?>
    <a href="index.php" class="d-block mt-3">&larr; Kembali ke daftar</a>
  </div>
</div>
<?php include 'includes/footer.php'; ?>