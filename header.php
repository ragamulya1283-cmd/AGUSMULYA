<?php
require __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Showroom Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .hero { background: linear-gradient(135deg,#0d6efd22,#6610f722); padding:60px 0; }
      .card-img-top { object-fit:cover; height:200px; }
      .nav-brand { font-weight:700; letter-spacing:.5px; }
    </style>
  </head>
  <body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand nav-brand" href="<?=BASE_URL?>">ShowroomMobil</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>?page=mobil">Mobil</a></li>
        <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>?page=kontak">Kontak</a></li>
        <?php if (!is_logged()): ?>
          <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>?page=login">Masuk</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2" href="<?=BASE_URL?>?page=daftar">Daftar</a></li>
        <?php else: ?>
          <?php if (is_admin()): ?>
            <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>admin/dashboard.php">Admin</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>user/dashboard.php">Dashboard</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?=BASE_URL?>proses/logout.php">Keluar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>