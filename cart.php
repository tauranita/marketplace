<?php require 'includes/db.php';
$cart = &$_SESSION['cart']; $cart = $cart ?? [];
function stock_of($db, $id) { $s = $db->prepare("SELECT stock FROM products WHERE id=?"); $s->bind_param('i', $id); $s->execute(); $r = $s->get_result()->fetch_assoc(); return $r ? (int)$r['stock'] : 0; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0); $qty = max(1, (int)($_POST['qty'] ?? 1)); $act = $_POST['action'] ?? '';
  if ($act === 'add') {
    $new = ($cart[$id] ?? 0) + $qty; $max = stock_of($db, $id);
    if ($max < 1) flash('Produk tidak tersedia.');
    else { $cart[$id] = min($new, $max); flash($new > $max ? 'Jumlah disesuaikan dengan stok.' : 'Produk ditambahkan ke keranjang.'); }
  } elseif ($act === 'update' && isset($cart[$id])) { $cart[$id] = min($qty, max(1, stock_of($db, $id))); }
  elseif ($act === 'remove') { unset($cart[$id]); flash('Produk dihapus dari keranjang.'); }
  header("Location: cart.php"); exit;
}
$items = []; $total = 0;
if ($cart) {
  $ids = implode(',', array_map('intval', array_keys($cart)));
  $res = $db->query("SELECT id,name,price,stock,image_url FROM products WHERE id IN ($ids)");
  foreach ($res as $r) { $r['qty'] = $cart[$r['id']]; $r['sub'] = $r['qty'] * $r['price']; $total += $r['sub']; $items[] = $r; }
}
$title = 'Keranjang Belanja'; include 'includes/header.php'; ?>
<h4 class="mb-3">Keranjang Belanja</h4>
<?php if (!$items): ?><p class="text-muted">Keranjang masih kosong. <a href="index.php">Belanja sekarang</a></p>
<?php else: ?>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
<thead><tr><th>Produk</th><th>Harga</th><th style="width:150px">Qty</th><th>Subtotal</th><th></th></tr></thead><tbody>
<?php foreach ($items as $i): ?><tr>
  <td><?= e($i['name']) ?></td><td><?= rp($i['price']) ?></td>
  <td><form method="post" class="d-flex gap-1"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $i['id'] ?>">
    <input type="number" name="qty" min="1" max="<?= $i['stock'] ?>" value="<?= $i['qty'] ?>" class="form-control form-control-sm"><button class="btn btn-sm btn-secondary">Ubah</button></form></td>
  <td><?= rp($i['sub']) ?></td>
  <td><form method="post"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= $i['id'] ?>"><button class="btn btn-sm btn-danger">Hapus</button></form></td>
</tr><?php endforeach; ?>
<tr class="table-light"><th colspan="3" class="text-end">Total</th><th colspan="2"><?= rp($total) ?></th></tr>
</tbody></table></div>
<a href="index.php" class="btn btn-outline-secondary">Lanjut Belanja</a>
<a href="checkout.php" class="btn btn-success">Checkout</a>
<?php endif; include 'includes/footer.php'; ?>