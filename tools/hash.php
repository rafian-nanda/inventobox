<?php
$password = 'pw'; // ganti sesuai yang ingin kamu hash
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hash;
