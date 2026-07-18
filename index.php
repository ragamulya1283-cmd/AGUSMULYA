<?php
global $pdo;
$mobil = $pdo->query("SELECT m.*, b.nama as merek FROM mobil m LEFT JOIN merek b ON m.merek_id=b.id ORDER BY m.id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="hero text-center text-dark">
    <div class="container">
        <h1 class="display-5">Showroom Mobil Terpercaya</h1>
        <p class="lead">Temukan mobil impian Anda — koleksi terbaru dan harga kompetitif.</p>
        <a href="<?=BASE_URL?>?page=mobil" class="btn btn-primary btn-lg">Lihat Semua Mobil</a>
    </div>
</section>

<div class="container py-5">
    <h3 class="mb-4">Mobil Unggulan</h3>
    <div class="row g-3">
        <?php foreach($mobil as $m): ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <img src="<?=htmlspecialchars(get_image_url($m['gambar'], 'https://via.placeholder.com/800x600'))?>"
                    class="card-img-top" alt="">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?=htmlspecialchars($m['nama'])?></h5>
                    <p class="text-muted small"><?=htmlspecialchars($m['merek'])?> • <?=htmlspecialchars($m['tahun'])?>
                        • <?=number_format((int)($m['kilometer'] ?? 0), 0, ',', '.')?> km</p>
                    <p class="mt-auto"><strong>Rp <?=number_format($m['harga'],0,',','.')?></strong></p>
                    <a href="<?=BASE_URL?>?page=mobil-detail&id=<?=$m['id']?>"
                        class="btn btn-outline-primary btn-sm">Detail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>