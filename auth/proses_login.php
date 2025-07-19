<?php
session_start();
require_once '../database/koneksi.php';
require_once '../config.php'; // base_url

$username = $_POST['username'];
$password = $_POST['password'];

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' LIMIT 1");

$user = mysqli_fetch_assoc($query);


// Cek password
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'toko') {
        $_SESSION['tipe_toko'] = $user['tipe_toko']; // online / offline
    }

    // Redirect ke dashboard sesuai role
    if ($user['role'] === 'admin') {
        header('Location: ' . $base_url . 'pages/admin/dashboard.php');
    } elseif ($user['role'] === 'gudang') {
        header('Location: ' . $base_url . 'pages/gudang/dashboard.php');
    } elseif ($user['role'] === 'toko') {
        if ($user['tipe_toko'] === 'online') {
            header('Location: ' . $base_url . 'pages/toko/dashboard_online.php');
        } else {
            header('Location: ' . $base_url . 'pages/toko/dashboard_offline.php');
        }
    }
    exit;
} else {
    $_SESSION['error'] = 'Username atau password salah';
    header('Location: login.php');
}
