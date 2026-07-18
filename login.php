<?php
require __DIR__.'/../config/config.php';
$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';
if (!$email || !$pass) {
    $_SESSION['flash'] = 'Email dan password diperlukan';
    header('Location: '.BASE_URL.'?page=login');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($pass, $user['password'])) {
    unset($user['password']);
    $_SESSION['user'] = $user;
    if ($user['role'] === 'admin') header('Location: '.BASE_URL.'admin/dashboard.php');
    else header('Location: '.BASE_URL.'user/dashboard.php');
    exit;
} else {
    $_SESSION['flash'] = 'Email atau password salah';
    header('Location: '.BASE_URL.'?page=login');
    exit;
}
?>