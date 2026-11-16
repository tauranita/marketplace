<?php require 'includes/db.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['status'], ['baru','diproses','dikirim','selesai','batal'], true)) {
  $s = $db->prepare("UPDATE orders SET status=? WHERE id=?"); $oid = (int)$_POST['id']; $s->bind_param('si', $_POST['status'], $oid); $s->execute();
  flash('Status pesanan diperbarui.'); header('Location: admin_orders.php'); exit;
}
$rows = $db->query("SELECT o.*, (SELECT GROUP_CONCAT(CONCAT(p.name,' x',oi.qty) SEPARATOR ', ') FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=o.id) items FROM orders o ORDER BY o.id DESC");
$title = 'Pesanan Masuk'; include 'includes/header.php'; ?>
<h4 class="mb-3">Pesanan Masuk</h4>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
<thead><tr><th>Kode</th><th>Pembeli</th><th>Item</th><th>Total</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>
<?php foreach ($rows as $o): ?><tr>
  <td><?= e($o['order_code']) ?></td><td><?= e($o['buyer_name']) ?><br><small><?= e($o['buyer_phone']) ?></small></td><td><?= e($o['items']) ?></td>
  <td><?= rp($o['total']) ?></td><td><?= e($o['created_at']) ?></td>
  <td><form method="post" class="d-flex gap-1"><input type="hidden" name="id" value="<?= $o['id'] ?>">
    <select name="status" class="form-select form-select-sm"><?php foreach (['baru','diproses','dikirim','selesai','batal'] as $st): ?><option <?= $o['status'] === $st ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select>
    <button class="btn btn-sm btn-primary">OK</button></form></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php include 'includes/footer.php'; ?>