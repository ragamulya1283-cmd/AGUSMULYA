<?php
require __DIR__ . '/../config/config.php';
if (!is_admin()) {
    header('Location: '.BASE_URL);
    exit;
}
$transaksi_id = intval($_POST['transaksi_id'] ?? 0);
$status = trim($_POST['status'] ?? '');
if (!$transaksi_id || !$status) {
    $_SESSION['flash'] = 'Data status transaksi tidak lengkap';
    header('Location: '.BASE_URL.'admin/transaksi.php');
    exit;
}
$allowed = ['pending','selesai','dibatalkan'];
if (!in_array($status, $allowed, true)) {
    $_SESSION['flash'] = 'Status tidak valid';
    header('Location: '.BASE_URL.'admin/transaksi.php');
    exit;
}
$pdo->prepare("UPDATE transaksi SET status=? WHERE id=?")->execute([$status, $transaksi_id]);
$_SESSION['flash'] = 'Status transaksi diperbarui';
header('Location: '.BASE_URL.'admin/transaksi.php');
exit;