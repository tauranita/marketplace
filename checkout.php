<?php require 'includes/db.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Keranjang kosong.'); header('Location: cart.php'); exit; }
$err = ''; $f = ['name' => '', 'phone' => '', 'address' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');
  if (!$f['name'] || !$f['phone'] || !$f['address']) $err = 'Semua data pembeli wajib diisi.';
  elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $f['phone'])) $err = 'Nomor telepon tidak valid.';
  else {
    try {
      $db->begin_transaction();
      $lines = []; $total = 0;
      foreach ($cart as $pid => $qty) {   // kunci baris produk agar stok aman
        $s = $db->prepare("SELECT id,name,price,stock FROM products WHERE id=? FOR UPDATE");
        $s->bind_param('i', $pid); $s->execute(); $p = $s->get_result()->fetch_assoc();
        if (!$p || $p['stock'] < $qty) throw new Exception('Stok tidak cukup untuk produk: ' . ($p['name'] ?? "#$pid"));
        $lines[] = [$p, $qty]; $total += $p['price'] * $qty;
      }
      $code = 'ORD' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
      $o = $db->prepare("INSERT INTO orders (order_code,buyer_name,buyer_phone,buyer_address,total) VALUES (?,?,?,?,?)");
      $o->bind_param('ssssd', $code, $f['name'], $f['phone'], $f['address'], $total); $o->execute();
      $oid = $db->insert_id;
      $i = $db->prepare("INSERT INTO order_items (order_id,product_id,qty,price,subtotal) VALUES (?,?,?,?,?)");
      $u = $db->prepare("UPDATE products SET stock=stock-? WHERE id=?");
      foreach ($lines as [$p, $qty]) {
        $sub = $p['price'] * $qty; $pid = (int)$p['id'];
        $i->bind_param('iiidd', $oid, $pid, $qty, $p['price'], $sub); $i->execute();
        $u->bind_param('ii', $qty, $pid); $u->execute();
      }
      $db->commit(); unset($_SESSION['cart']);
      header('Location: order_success.php?code=' . $code); exit;
    } catch (Throwable $ex) { $db->rollback(); $err = $ex instanceof mysqli_sql_exception ? 'Terjadi kesalahan database.' : $ex->getMessage(); }
  }
}
$title = 'Checkout'; include 'includes/header.php'; ?>
<h4>Checkout</h4>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="card card-body" style="max-width:600px">
  <label class="form-label">Nama Lengkap</label><input class="form-control mb-2" name="name" value="<?= e($f['name']) ?>" required>
  <label class="form-label">No. Telepon</label><input class="form-control mb-2" name="phone" value="<?= e($f['phone']) ?>" required>
  <label class="form-label">Alamat Pengiriman</label><textarea class="form-control mb-3" name="address" rows="3" required><?= e($f['address']) ?></textarea>
  <button class="btn btn-success">Buat Pesanan</button>
</form>
<?php include 'includes/footer.php'; ?>