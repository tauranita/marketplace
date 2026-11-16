<?php $cartCount = array_sum($_SESSION['cart'] ?? []); $flash = flash(); ?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Marketplace') ?> - Marketplace Sederhana</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<nav class="navbar navbar-expand-md navbar-dark bg-primary mb-4"><div class="container">
  <a class="navbar-brand" href="index.php">🛒 Marketplace</a>
  <div class="ms-auto d-flex gap-2 align-items-center">
    <a class="btn btn-light btn-sm" href="cart.php">Keranjang <span class="badge bg-danger"><?= $cartCount ?></span></a>
    <?php if (!empty($_SESSION['user'])): ?>
      <a class="btn btn-outline-light btn-sm" href="admin_products.php">Kelola Produk</a>
      <a class="btn btn-outline-light btn-sm" href="admin_orders.php">Pesanan</a>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Keluar</a>
    <?php else: ?>
      <a class="btn btn-outline-light btn-sm" href="login.php">Login Penjual</a>
    <?php endif; ?>
  </div></div></nav>
<div class="container">
<?php if ($flash): ?><div class="alert alert-info"><?= e($flash) ?></div><?php endif; ?>