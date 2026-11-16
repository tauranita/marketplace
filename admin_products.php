<?php require 'includes/db.php'; require_login();
$s = $db->prepare("SELECT p.*, c.name cname FROM products p JOIN categories c ON c.id=p.category_id WHERE seller_id=? ORDER BY p.id DESC");
$s->bind_param('i', $_SESSION['user']['id']); $s->execute(); $rows = $s->get_result();
$title = 'Kelola Produk'; include 'includes/header.php'; ?>
<div class="d-flex justify-content-between mb-3"><h4>Kelola Produk</h4><a href="admin_product_form.php" class="btn btn-success">+ Tambah Produk</a></div>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
<thead><tr><th>#</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $p): ?><tr>
  <td><?= $p['id'] ?></td><td><?= e($p['name']) ?></td><td><?= e($p['cname']) ?></td><td><?= rp($p['price']) ?></td><td><?= $p['stock'] ?></td>
  <td class="d-flex gap-1"><a class="btn btn-sm btn-warning" href="admin_product_form.php?id=<?= $p['id'] ?>">Edit</a>
    <form method="post" action="admin_product_delete.php" onsubmit="return confirm('Hapus produk ini?')"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn btn-sm btn-danger">Hapus</button></form></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php include 'includes/footer.php'; ?>