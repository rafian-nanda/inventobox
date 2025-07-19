<?php
// ajax/toggle_status.php
header('Content-Type: application/json');
require_once '../database/koneksi.php';

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'msg' => 'No ID']);
    exit;
}
$id = (int)$_POST['id'];
$res = mysqli_query($conn, "SELECT status FROM produk WHERE id = $id");
if (!$res || !($row = mysqli_fetch_assoc($res))) {
    echo json_encode(['success' => false, 'msg' => 'Not found']);
    exit;
}
$newStatus = $row['status'] == 1 ? 0 : 1;
$update = mysqli_query($conn, "UPDATE produk SET status = $newStatus WHERE id = $id");
if ($update) {
    echo json_encode(['success' => true, 'status' => $newStatus]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Update failed']);
}
