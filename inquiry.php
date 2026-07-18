<?php
require __DIR__.'/../config/config.php';
$mobil_id = intval($_POST['mobil_id'] ?? 0);
if (!is_logged()) {
    $_SESSION['flash'] = 'Silakan masuk terlebih dahulu untuk melakukan inquiry';
    header('Location: '.BASE_URL.'?page=login'); exit;
}
$ins = $pdo->prepare("INSERT INTO inquiry (mobil_id,user_id,created_at) VALUES (?,?,NOW())");
$ins->execute([$mobil_id, $_SESSION['user']['id']]);
$_SESSION['flash'] = 'Inquiry terkirim, tim kami akan menghubungi Anda.';
header('Location: '.BASE_URL.'?page=mobil-detail&id='.$mobil_id);
exit;
?>