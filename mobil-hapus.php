<?php
require __DIR__.'/../config/config.php';
require_admin();
$id = intval($_GET['id'] ?? 0);
if ($id) {
    // hapus file gambar jika ada di uploads
    $stmt = $pdo->prepare("SELECT gambar FROM mobil WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['gambar'] && strpos($row['gambar'], UPLOAD_URL) === 0) {
        $rel = substr($row['gambar'], strlen(UPLOAD_URL));
        $path = UPLOAD_DIR . $rel;
        if (is_file($path)) @unlink($path);
    }
    $pdo->prepare("DELETE FROM mobil WHERE id=?")->execute([$id]);
}
header('Location: '.BASE_URL.'admin/mobil-list.php');
exit;
?>