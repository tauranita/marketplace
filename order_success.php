<?php require 'includes/db.php';
$code = $_GET['code'] ?? '';
$s = $db->prepare("SELECT * FROM orders WHERE order_code=?"); $s->bind_param('s', $code); $s->execute();
$o = $s->get_result()->fetch_assoc(); if (!$o) { http_response_code(404); die('Pesanan tidak ditemukan.'); }
$s = $db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE order_id=?");
$s->bind_param('i', $o['id']); $s->execute(); $items = $s->get_result();
$title = 'Pesanan Berhasil'; include 'includes/header.php'; ?>
<div class="alert alert-success">Pesanan berhasil dibuat! Kode pesanan: <strong><?= e($o['order_code']) ?></strong></div>
<p>Pembeli: <?= e($o['buyer_name']) ?> (<?= e($o['buyer_phone']) ?>)<br>Alamat: <?= e($o['buyer_address']) ?><br>Status: <span class="badge bg-info"><?= e($o['status']) ?></span></p>
<table class="table table-bordered bg-white"><thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>
<?php foreach ($items as $i): ?><tr><td><?= e($i['name']) ?></td><td><?= rp($i['price']) ?></td><td><?= $i['qty'] ?></td><td><?= rp($i['subtotal']) ?></td></tr><?php endforeach; ?>
<tr><th colspan="3" class="text-end">Total</th><th><?= rp($o['total']) ?></th></tr></tbody></table>
<a href="index.php" class="btn btn-primary">Kembali Belanja</a>
<?php include 'includes/footer.php'; ?>