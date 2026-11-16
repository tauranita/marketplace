<?php require 'includes/db.php'; require_login();
$uid = $_SESSION['user']['id']; $id = (int)($_GET['id'] ?? 0);
$p = ['name' => '', 'category_id' => 1, 'description' => '', 'price' => '', 'stock' => '', 'image_url' => ''];
if ($id) {
  $s = $db->prepare("SELECT * FROM products WHERE id=? AND seller_id=?"); $s->bind_param('ii', $id, $uid); $s->execute();
  $p = $s->get_result()->fetch_assoc(); if (!$p) die('Produk tidak ditemukan.');
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $p = array_merge($p, ['name' => trim($_POST['name']), 'category_id' => (int)$_POST['category_id'], 'description' => trim($_POST['description']),
    'price' => (float)$_POST['price'], 'stock' => (int)$_POST['stock'], 'image_url' => trim($_POST['image_url'])]);
  if ($p['name'] === '' || $p['price'] <= 0 || $p['stock'] < 0) $err = 'Nama wajib diisi, harga > 0, stok >= 0.';
  elseif ($p['image_url'] !== '' && !filter_var($p['image_url'], FILTER_VALIDATE_URL)) $err = 'URL gambar tidak valid.';
  else {
    if ($id) { $s = $db->prepare("UPDATE products SET name=?,category_id=?,description=?,price=?,stock=?,image_url=? WHERE id=? AND seller_id=?");
      $s->bind_param('sisdisii', $p['name'], $p['category_id'], $p['description'], $p['price'], $p['stock'], $p['image_url'], $id, $uid); }
    else { $s = $db->prepare("INSERT INTO products (name,category_id,description,price,stock,image_url,seller_id) VALUES (?,?,?,?,?,?,?)");
      $s->bind_param('sisdisi', $p['name'], $p['category_id'], $p['description'], $p['price'], $p['stock'], $p['image_url'], $uid); }
    $s->execute(); flash('Produk berhasil disimpan.'); header('Location: admin_products.php'); exit;
  }
}
$cats = $db->query("SELECT * FROM categories ORDER BY name");
$title = $id ? 'Edit Produk' : 'Tambah Produk'; include 'includes/header.php'; ?>
<h4><?= e($title) ?></h4>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="card card-body" style="max-width:650px">
  <label class="form-label">Nama Produk</label><input class="form-control mb-2" name="name" value="<?= e($p['name']) ?>" required>
  <label class="form-label">Kategori</label><select class="form-select mb-2" name="category_id">
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $p['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
  <label class="form-label">Deskripsi</label><textarea class="form-control mb-2" name="description" rows="3"><?= e($p['description']) ?></textarea>
  <div class="row"><div class="col"><label class="form-label">Harga (Rp)</label><input type="number" step="100" class="form-control mb-2" name="price" value="<?= e($p['price']) ?>" required></div>
  <div class="col"><label class="form-label">Stok</label><input type="number" class="form-control mb-2" name="stock" value="<?= e($p['stock']) ?>" required></div></div>
  <label class="form-label">URL Gambar</label><input class="form-control mb-3" name="image_url" value="<?= e($p['image_url']) ?>" placeholder="https://...">
  <div><button class="btn btn-primary">Simpan</button> <a href="admin_products.php" class="btn btn-secondary">Batal</a></div>
</form>
<?php include 'includes/footer.php'; ?>