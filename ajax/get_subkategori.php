<?php

require_once '../database/koneksi.php';

$parent_id = intval($_GET['parent_id']);
$query = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori WHERE parent_id = $parent_id");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
