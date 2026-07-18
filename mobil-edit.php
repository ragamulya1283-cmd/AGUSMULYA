<?php
require __DIR__.'/../config/config.php';
require_admin();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: '.BASE_URL.'admin/mobil-list.php'); exit; }

$merek = $pdo->query("SELECT * FROM merek")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $merek_id = intval($_POST['merek_id'] ?? 0);
    $kategori_id = intval($_POST['kategori_id'] ?? 0);
    $tahun = intval($_POST['tahun'] ?? 0);
    $kilometer = intval($_POST['kilometer'] ?? 0);
    $raw_harga = trim($_POST['harga'] ?? '0');
    $harga_clean = preg_replace('/[^0-9.,]/', '', $raw_harga);
    $harga_clean = str_replace([',','.'], '', $harga_clean);
    $harga = intval($harga_clean);
    $des = trim($_POST['deskripsi'] ?? '');
    $gambar_url = '';

    $old = $pdo->prepare("SELECT gambar FROM mobil WHERE id=?");
    $old->execute([$id]);
    $oldRow = $old->fetch(PDO::FETCH_ASSOC);
    $oldGambar = $oldRow['gambar'] ?? '';

    if ($nama === '' || $merek_id <= 0 || $kategori_id <= 0 || $tahun <= 0 || $harga <= 0 || $kilometer < 0) {
        $_SESSION['flash'] = 'Nama, merek, kategori, tahun, kilometer dan harga wajib diisi dengan benar.';
        header('Location: '.BASE_URL.'admin/mobil-edit.php?id='.$id); exit;
    }

    $gambar_url = $oldGambar;

    if (!empty($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Terjadi kesalahan upload (kode '.$file['error'].')';
            header('Location: '.BASE_URL.'admin/mobil-edit.php?id='.$id); exit;
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $_SESSION['flash'] = 'Ukuran file terlalu besar (maks '.(UPLOAD_MAX_SIZE/1024/1024).' MB)';
            header('Location: '.BASE_URL.'admin/mobil-edit.php?id='.$id); exit;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, UPLOAD_ALLOWED)) {
            $_SESSION['flash'] = 'Tipe file tidak diperbolehkan. Hanya JPG/PNG/WEBP.';
            header('Location: '.BASE_URL.'admin/mobil-edit.php?id='.$id); exit;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
            $_SESSION['flash'] = 'Gagal menyimpan file upload.';
            header('Location: '.BASE_URL.'admin/mobil-edit.php?id='.$id); exit;
        }
        if ($oldGambar && strpos($oldGambar, UPLOAD_URL) === 0) {
            $rel = substr($oldGambar, strlen(UPLOAD_URL));
            $oldPath = UPLOAD_DIR . $rel;
            if (is_file($oldPath)) @unlink($oldPath);
        }
        $gambar_url = UPLOAD_URL . $newName;
    }

    $stmt = $pdo->prepare("UPDATE mobil SET nama=?, merek_id=?, kategori_id=?, tahun=?, kilometer=?, harga=?, deskripsi=?, gambar=? WHERE id=?");
    $stmt->execute([$nama,$merek_id,$kategori_id,$tahun,$kilometer,$harga,$des,$gambar_url,$id]);
    $_SESSION['flash'] = 'Data mobil diperbarui';
    header('Location: '.BASE_URL.'admin/mobil-list.php'); exit;
}

$stmt = $pdo->prepare("SELECT * FROM mobil WHERE id=?");
$stmt->execute([$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$m) { header('Location: '.BASE_URL.'admin/mobil-list.php'); exit; }

include __DIR__ . '/../config/header.php';
?>
<div class="container py-5">
    <h3>Edit Mobil</h3>
    <?php if(!empty($_SESSION['flash'])){ echo "<div class='alert alert-info'>".$_SESSION['flash']."</div>"; unset($_SESSION['flash']); } ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label>Nama</label><input name="nama" value="<?=htmlspecialchars($m['nama'])?>" class="form-control" required></div>
        <div class="mb-3"><label>Merek</label>
            <select name="merek_id" class="form-control">
                <?php foreach($merek as $b): ?><option value="<?=$b['id']?>" <?=($b['id']==$m['merek_id']?'selected':'')?>><?=htmlspecialchars($b['nama'])?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3"><label>Kategori</label>
            <select name="kategori_id" class="form-control">
                <?php foreach($kategori as $k): ?><option value="<?=$k['id']?>" <?=($k['id']==$m['kategori_id']?'selected':'')?>><?=htmlspecialchars($k['nama'])?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3"><label>Tahun</label><input name="tahun" value="<?=htmlspecialchars($m['tahun'])?>" class="form-control" required></div>
        <div class="mb-3"><label>Kilometer</label><input name="kilometer" value="<?=htmlspecialchars($m['kilometer'] ?? 0)?>" class="form-control" required></div>
        <div class="mb-3"><label>Harga</label><input name="harga" value="<?=htmlspecialchars($m['harga'])?>" class="form-control" required></div>
        <div class="mb-3">
            <label>Upload Gambar Baru (opsional, JPG/PNG/WEBP, max <?=UPLOAD_MAX_SIZE/1024/1024?> MB)</label>
            <input type="file" name="gambar_file" accept="image/*" class="form-control">
            <div class="form-text">Kosongkan jika tidak ingin mengganti gambar yang sudah tersimpan.</div>
        </div>
        <div class="mb-3">
            <label>Preview</label>
            <div class="border rounded p-2 text-center">
                <img id="preview-gambar" src="<?=htmlspecialchars(get_image_url($m['gambar'], 'https://via.placeholder.com/800x600'))?>" class="img-fluid rounded" style="max-height:220px;" alt="Preview gambar mobil">
            </div>
        </div>
        <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" class="form-control"><?=htmlspecialchars($m['deskripsi'])?></textarea></div>
        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
<?php include __DIR__ . '/../config/footer.php'; ?>