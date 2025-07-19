<?php
session_start();
$_SESSION = []; // Kosongkan semua isi session
session_unset(); // Hapus semua variabel session
session_destroy(); // Hancurkan session

// Hapus juga cookie session agar benar-benar bersih
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.php");
exit;
