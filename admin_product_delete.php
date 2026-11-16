<?php require 'includes/db.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try { $s = $db->prepare("DELETE FROM products WHERE id=? AND seller_id=?"); $id = (int)$_POST['id']; $s->bind_param('ii', $id, $_SESSION['user']['id']); $s->execute(); flash('Produk dihapus.'); }
  catch (mysqli_sql_exception $e) { flash('Produk tidak bisa dihapus karena sudah ada di pesanan. Set stok ke 0 sebagai gantinya.'); }
}
header('Location: admin_products.php');