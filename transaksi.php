<?php
require __DIR__ . '/../config/config.php';
if (!is_logged()) {
    $_SESSION['flash'] = 'Silakan masuk untuk membeli mobil';
    header('Location: '.BASE_URL.'?page=login');
    exit;
}
$mobil_id = intval($_POST['mobil_id'] ?? 0);
if (!$mobil_id) {
    $_SESSION['flash'] = 'Mobil tidak valid';
    header('Location: '.BASE_URL.'?page=mobil');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM mobil WHERE id=?");
$stmt->execute([$mobil_id]);
$mobil = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$mobil) {
    $_SESSION['flash'] = 'Mobil tidak ditemukan';
    header('Location: '.BASE_URL.'?page=mobil');
    exit;
}
if ($mobil['stok'] <= 0) {
    $_SESSION['flash'] = 'Maaf, stok mobil ini sudah habis';
    header('Location: '.BASE_URL.'?page=mobil-detail&id='.$mobil_id);
    exit;
}
$pdo->beginTransaction();
try {
    $insert = $pdo->prepare("INSERT INTO transaksi (user_id, mobil_id, total, status, created_at) VALUES (?,?,?, 'pending', NOW())");
    $insert->execute([$_SESSION['user']['id'], $mobil_id, $mobil['harga']]);
    $update = $pdo->prepare("UPDATE mobil SET stok = stok - 1 WHERE id = ? AND stok > 0");
    $update->execute([$mobil_id]);
    $pdo->commit();
    $_SESSION['flash'] = 'Transaksi berhasil dibuat. Silakan cek riwayat di dashboard.';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = 'Gagal memproses transaksi. Silakan coba lagi.';
}
header('Location: '.BASE_URL.'user/dashboard.php');
exit;
