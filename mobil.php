<?php
global $pdo;
$q = $_GET['q'] ?? '';
if ($q) {
    $stmt = $pdo->prepare("SELECT m.*, b.nama as merek FROM mobil m LEFT JOIN merek b ON m.merek_id=b.id WHERE m.nama LIKE ? OR b.nama LIKE ? ORDER BY m.id DESC");
    $stmt->execute(["%$q%","%$q%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $rows = $pdo->query("SELECT m.*, b.nama as merek FROM mobil m LEFT JOIN merek b ON m.merek_id=b.id ORDER BY m.id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Mobil</h3>
    <form class="d-flex" method="get">
      <input type="hidden" name="page" value="mobil">
      <input class="form-control me-2" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Cari mobil atau merek...">
      <button class="btn btn-outline-secondary">Cari</button>
    </form>
  </div>
  <div class="row g-3">
    <?php foreach($rows as $m): ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="<?=htmlspecialchars(get_image_url($m['gambar'], 'https://via.placeholder.com/800x600'))?>" class="card-img-top" alt="">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?=htmlspecialchars($m['nama'])?></h5>
            <p class="small text-muted"><?=htmlspecialchars($m['merek'])?> • <?=htmlspecialchars($m['tahun'])?> • <?=number_format((int)($m['kilometer'] ?? 0), 0, ',', '.')?> km</p>
            <p class="mt-auto"><strong>Rp <?=number_format($m['harga'],0,',','.')?></strong></p>
            <a href="<?=BASE_URL?>?page=mobil-detail&id=<?=$m['id']?>" class="btn btn-primary btn-sm">Detail</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>