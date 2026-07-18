<?php
require __DIR__.'/../config/config.php';
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';
if (!$nama || !$email || !$pass) {
    $_SESSION['flash'] = 'Semua field harus diisi';
    header('Location: '.BASE_URL.'?page=daftar'); exit;
}
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['flash'] = 'Email sudah digunakan';
    header('Location: '.BASE_URL.'?page=daftar'); exit;
}
$hash = password_hash($pass, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO users (nama,email,password,role,created_at) VALUES (?,?,?,? ,NOW())");
$ins->execute([$nama,$email,$hash,'user']);
$_SESSION['flash'] = 'Pendaftaran berhasil. Silakan masuk.';
header('Location: '.BASE_URL.'?page=login');
exit;
?>