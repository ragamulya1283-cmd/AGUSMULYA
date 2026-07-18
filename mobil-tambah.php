<?php
require __DIR__.'/../config/config.php';
require_admin();
$merek = $pdo->query("SELECT * FROM merek")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $merek_id = intval($_POST['merek_id'] ?? 0);
    $kategori_id = intval($_POST['kategori_id'] ?? 0);
    $tahun = intval($_POST['tahun'] ?? 0);
    $kilometer = intval($_POST['kilometer'] ?? 0);
    // normalize harga: remove non-digit except comma/dot, convert to integer
    $raw_harga = trim($_POST['harga'] ?? '0');
    $harga_clean = preg_replace('/[^0-9.,]/', '', $raw_harga);
    $harga_clean = str_replace([',','.'], '', $harga_clean);
    $harga = intval($harga_clean);
    $des = trim($_POST['deskripsi'] ?? '');
    $gambar_url = '';

    // basic validation
    if ($nama === '' || $merek_id <= 0 || $kategori_id <= 0 || $tahun <= 0 || $harga <= 0 || $kilometer < 0) {
      $_SESSION['flash'] = 'Nama, merek, kategori, tahun, kilometer dan harga wajib diisi dengan benar.';
      header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
    }

    if (empty($_FILES['gambar_file']['name']) || ($_FILES['gambar_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $_SESSION['flash'] = 'Silakan pilih file gambar untuk mobil.';
        header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
    }

    // handle upload
    if (!empty($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Terjadi kesalahan upload (kode '.$file['error'].')';
            header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $_SESSION['flash'] = 'Ukuran file terlalu besar (maks '.(UPLOAD_MAX_SIZE/1024/1024).' MB)';
            header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, UPLOAD_ALLOWED)) {
            $_SESSION['flash'] = 'Tipe file tidak diperbolehkan. Hanya JPG/PNG/WEBP.';
            header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!is_dir(UPLOAD_DIR) && !@mkdir(UPLOAD_DIR, 0755, true)) {
          $_SESSION['flash'] = 'Folder upload tidak tersedia.';
          header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
        }
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
          $_SESSION['flash'] = 'Gagal menyimpan file upload.';
          header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
        }
        $gambar_url = UPLOAD_URL . $newName;
    }

      try {
        $stmt = $pdo->prepare("INSERT INTO mobil (nama,merek_id,kategori_id,tahun,kilometer,harga,deskripsi,gambar,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([$nama,$merek_id,$kategori_id,$tahun,$kilometer,$harga,$des,$gambar_url]);
        $_SESSION['flash'] = 'Mobil berhasil ditambahkan';
        // redirect to admin dashboard so counts and recent list refresh immediately
        header('Location: '.BASE_URL.'admin/dashboard.php'); exit;
      } catch (Exception $e) {
        $_SESSION['flash'] = 'Gagal menyimpan data mobil: ' . $e->getMessage();
        header('Location: '.BASE_URL.'admin/mobil-tambah.php'); exit;
      }
}
include __DIR__ . '/../config/header.php';
?>
<div class="container py-5">
  <h3>Tambah Mobil</h3>
  <?php if(!empty($_SESSION['flash'])){ echo "<div class='alert alert-info'>".$_SESSION['flash']."</div>"; unset($_SESSION['flash']); } ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3"><label>Nama</label><input name="nama" class="form-control" required></div>
    <div class="mb-3"><label>Merek</label>
      <select name="merek_id" class="form-control">
        <?php foreach($merek as $b): ?><option value="<?=$b['id']?>"><?=htmlspecialchars($b['nama'])?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label>Kategori</label>
      <select name="kategori_id" class="form-control">
        <?php foreach($kategori as $k): ?><option value="<?=$k['id']?>"><?=htmlspecialchars($k['nama'])?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label>Tahun</label><input name="tahun" class="form-control" required></div>
    <div class="mb-3"><label>Kilometer</label><input name="kilometer" class="form-control" required placeholder="Contoh: 15000"></div>
    <div class="mb-3"><label>Harga</label><input name="harga" class="form-control" required></div>

    <div class="mb-3">
      <label>Upload Gambar (wajib, JPG/PNG/WEBP, max <?=UPLOAD_MAX_SIZE/1024/1024?> MB)</label>
      <input type="file" name="gambar_file" accept="image/*" class="form-control" required>
      <div class="form-text">Gambar diupload langsung dari perangkat. Tidak perlu mengisi URL gambar.</div>
    </div>

    <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" class="form-control"></textarea></div>
    <button class="btn btn-success">Simpan</button>
  </form>
</div>
<?php include __DIR__ . '/../config/footer.php'; ?>