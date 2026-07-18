<?php
require __DIR__.'/../config/config.php';
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');
if (!$nama || !$email || !$pesan) { $_SESSION['flash'] = 'Lengkapi formulir'; header('Location: '.BASE_URL.'?page=kontak'); exit; }
$pdo->prepare("INSERT INTO kontak (nama,email,pesan,created_at) VALUES (?,?,?,NOW())")->execute([$nama,$email,$pesan]);
$_SESSION['flash'] = 'Pesan terkirim, terima kasih.';
header('Location: '.BASE_URL.'?page=kontak');
exit;
?>