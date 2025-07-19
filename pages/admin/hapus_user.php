<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: /index.php');
  exit;
}
require_once __DIR__ . '/../../database/koneksi.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $query = "DELETE FROM users WHERE id = $id";
  mysqli_query($conn, $query);
}
header('Location: manajemen_user.php');
exit;
?>
