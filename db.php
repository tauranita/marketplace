<?php
session_start();
require __DIR__ . '/../config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try { $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); $db->set_charset('utf8mb4'); }
catch (Throwable $e) { die('Koneksi database gagal. Periksa config.php.'); }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function require_login() { if (empty($_SESSION['user'])) { header('Location: login.php'); exit; } }
function flash($msg = null) {
  if ($msg !== null) { $_SESSION['flash'] = $msg; return; }
  $m = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); return $m;
}