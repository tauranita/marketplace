<?php require 'includes/db.php'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $s = $db->prepare("SELECT * FROM users WHERE email=? AND role='penjual'"); $s->bind_param('s', $_POST['email']); $s->execute();
  $u = $s->get_result()->fetch_assoc();
  if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
    session_regenerate_id(true); $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name']];
    header('Location: admin_products.php'); exit;
  } $err = 'Email atau password salah.';
}
$title = 'Login Penjual'; include 'includes/header.php'; ?>
<h4>Login Penjual</h4>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="card card-body" style="max-width:400px">
  <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
  <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
  <button class="btn btn-primary">Masuk</button>
</form>
<?php include 'includes/footer.php'; ?>