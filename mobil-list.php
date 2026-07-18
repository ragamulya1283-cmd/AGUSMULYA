<?php
require __DIR__.'/../config/config.php';
require_admin();
$rows = $pdo->query("SELECT m.*, b.nama as merek FROM mobil m LEFT JOIN merek b ON m.merek_id=b.id ORDER BY m.id DESC")->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/../config/header.php';
?>
<div class="container py-5">
  <h3>Daftar Mobil <a href="<?=BASE_URL?>admin/mobil-tambah.php" class="btn btn-success btn-sm">Tambah</a></h3>
  <?php if(!empty($_SESSION['flash'])){ echo "<div class='alert alert-info'>".$_SESSION['flash']."</div>"; unset($_SESSION['flash']); } ?>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Nama</th><th>Merek</th><th>Harga</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
      <tr>
        <td><?=$r['id']?></td>
        <td><?=$r['nama']?></td>
        <td><?=$r['merek']?></td>
        <td>Rp <?=number_format($r['harga'],0,',','.')?></td>
        <td>
          <a href="<?=BASE_URL?>admin/mobil-edit.php?id=<?=$r['id']?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="<?=BASE_URL?>admin/mobil-hapus.php?id=<?=$r['id']?>" onclick="return confirm('Hapus?')" class="btn btn-sm btn-danger">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../config/footer.php'; ?>