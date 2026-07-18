<?php
require __DIR__.'/../config/config.php';
if (!is_logged()) { $_SESSION['flash'] = 'Silakan masuk untuk wishlist'; header('Location: '.BASE_URL.'?page=login'); exit; }
$mobil_id = intval($_POST['mobil_id'] ?? 0);
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND mobil_id=?");
$stmt->execute([$user_id,$mobil_id]);
if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND mobil_id=?")->execute([$user_id,$mobil_id]);
    $_SESSION['flash'] = 'Dihapus dari wishlist';
} else {
    $pdo->prepare("INSERT INTO wishlist (user_id,mobil_id,created_at) VALUES (?,?,NOW())")->execute([$user_id,$mobil_id]);
    $_SESSION['flash'] = 'Ditambahkan ke wishlist';
}
header('Location: '.BASE_URL.'?page=mobil-detail&id='.$mobil_id);
exit;
?>