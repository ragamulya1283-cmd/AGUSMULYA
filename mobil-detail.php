<?php
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT m.*, b.nama as merek, k.nama as kategori FROM mobil m LEFT JOIN merek b ON m.merek_id=b.id LEFT JOIN kategori k ON m.kategori_id=k.id WHERE m.id=?");
$stmt->execute([$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$m) { echo "<div class='container py-5'><h3>Mobil tidak ditemukan</h3></div>"; return; }
?>
<div class="container py-5">
  <div class="row">
    <div class="col-md-6">
      <img src="<?=htmlspecialchars(get_image_url($m['gambar'], 'https://via.placeholder.com/800x600'))?>" class="img-fluid rounded" alt="">
    </div>
    <div class="col-md-6">
      <h2><?=htmlspecialchars($m['nama'])?></h2>
      <p class="text-muted"><?=htmlspecialchars($m['merek'])?> • <?=htmlspecialchars($m['kategori'])?> • <?=htmlspecialchars($m['tahun'])?> • <?=number_format((int)($m['kilometer'] ?? 0), 0, ',', '.')?> km</p>
      <h4 class="text-primary">Rp <?=number_format($m['harga'],0,',','.')?></h4>
      <p><?=nl2br(htmlspecialchars($m['deskripsi']))?></p>
      <div class="d-flex gap-2 flex-wrap">
        <form action="proses/inquiry.php" method="post" class="m-0">
          <input type="hidden" name="mobil_id" value="<?=$m['id']?>">
          <button class="btn btn-outline-success">Tanyakan</button>
        </form>
        <form action="proses/wishlist.php" method="post" class="m-0">
          <input type="hidden" name="mobil_id" value="<?=$m['id']?>">
          <button class="btn btn-outline-primary">Tambah Wishlist</button>
        </form>
        <form action="proses/transaksi.php" method="post" class="m-0">
          <input type="hidden" name="mobil_id" value="<?=$m['id']?>">
          <button class="btn btn-primary">Beli Sekarang</button>
        </form>
      </div>
    </div>
  </div>
</div>