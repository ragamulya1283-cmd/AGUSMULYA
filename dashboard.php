<?php
require __DIR__ . '/../config/config.php';
require_login();
$rows = $pdo->prepare("SELECT t.*, m.nama AS mobil_nama, m.harga AS harga_mobil, m.gambar AS mobil_gambar FROM transaksi t JOIN mobil m ON m.id=t.mobil_id WHERE t.user_id = ? ORDER BY t.created_at DESC");
$rows->execute([$_SESSION['user']['id']]);
$transaksi = $rows->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/../config/header.php';
?>
<div class="container py-5">
    <h3>Dashboard Saya</h3>
    <?php if(!empty($_SESSION['flash'])){ echo "<div class='alert alert-info'>".$_SESSION['flash'].'</div>'; unset($_SESSION['flash']); } ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3">Nama <h5><?=htmlspecialchars($_SESSION['user']['nama'])?></h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">Email <h5><?=htmlspecialchars($_SESSION['user']['email'])?></h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">Transaksi <h5><?=count($transaksi)?></h5>
            </div>
        </div>
    </div>
    <h4>Riwayat Transaksi</h4>
    <?php if (empty($transaksi)): ?>
    <div class="alert alert-secondary">Belum ada transaksi.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mobil</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($transaksi as $row): ?>
                <tr>
                    <td><?=$row['id']?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?=htmlspecialchars(get_image_url($row['mobil_gambar'], 'https://via.placeholder.com/80x60'))?>"
                                width="80" class="img-thumbnail" alt="">
                            <?=htmlspecialchars($row['mobil_nama'])?>
                        </div>
                    </td>
                    <td>Rp <?=number_format($row['total'],0,',','.')?></td>
                    <td><?=htmlspecialchars($row['status'])?></td>
                    <td><?=htmlspecialchars($row['created_at'])?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../config/footer.php'; ?>