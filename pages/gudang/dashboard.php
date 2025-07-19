<?php 
// Mulai session
session_start();

// Cegah browser menyimpan halaman dashboard di cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gudang') {
    header("Location: /inventobox/auth/login.php");
    exit;
}

// Judul halaman
$pageTitle = "Dashboard Gudang";

// Mulai buffer output
ob_start();
?>

<!-- KONTEN UTAMA ADMIN -->
<h1>Dashboard Gudang</h1>
<p>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</p>

<?php
$content = ob_get_clean();

// Panggil layout utama
include '../../layouts/layout.php';
?>
