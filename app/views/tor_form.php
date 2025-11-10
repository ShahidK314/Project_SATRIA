<?php
$pdo = get_pdo();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
    $judul = $_POST['judul'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $total = floatval($_POST['total_anggaran'] ?? 0);
    $rab = $_POST['rab'] ?? '';

    $stmt = $pdo->prepare('INSERT INTO tor (pengusul_id, judul, tanggal_mulai, tanggal_selesai, lokasi, deskripsi, total_anggaran, rab, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    // handle file uploads (lampiran) dengan validasi
    $attachments = [];
    if (!empty($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
            if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['attachments']['tmp_name'][$i];
                $name = basename($_FILES['attachments']['name'][$i]);
                $saved = save_upload($tmp, $name);
                if ($saved) $attachments[] = $saved;
            }
        }
    }
    $attachments_json = json_encode($attachments);
    $stmt->execute([$user['id'], $judul, $tanggal_mulai, $tanggal_selesai, $lokasi, $deskripsi, $total, $rab, 'submitted']);
    // simpan attachments via update jika ada
    $lastId = $pdo->lastInsertId();
    if (!empty($attachments)) {
        $u = $pdo->prepare('UPDATE tor SET attachments = ? WHERE id = ?');
        $u->execute([$attachments_json, $lastId]);
    }
    flash_set('success', 'TOR berhasil disubmit.');
    header('Location: ' . BASE_URL . '/?page=tor_list');
    exit;
}

$success = flash_get('success');
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Buat TOR Baru</h2>
  <?php if($success): ?>
    <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= e($success) ?></div>
  <?php endif; ?>
  <form method="post" action="" class="space-y-4" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label>Judul Kegiatan
      <input name="judul" required class="w-full border p-2 rounded">
    </label>
    <div class="grid grid-cols-2 gap-4">
      <label>Tanggal Mulai
        <input type="date" name="tanggal_mulai" class="w-full border p-2 rounded">
      </label>
      <label>Tanggal Selesai
        <input type="date" name="tanggal_selesai" class="w-full border p-2 rounded">
      </label>
    </div>
    <label>Lokasi
      <input name="lokasi" class="w-full border p-2 rounded">
    </label>
    <label>Deskripsi Singkat
      <textarea name="deskripsi" class="w-full border p-2 rounded"></textarea>
    </label>
    <label>RAB (format JSON sederhana)
      <textarea name="rab" class="w-full border p-2 rounded" placeholder='[{"item":"Transport","qty":1,"harga":100000}]'></textarea>
    </label>
    <label>Lampiran (boleh lebih dari 1 file, max 10MB tiap file)
      <input type="file" name="attachments[]" multiple class="w-full">
    </label>
    <label>Total Anggaran (angka)
      <input name="total_anggaran" type="number" step="0.01" class="w-full border p-2 rounded">
    </label>
    <div class="flex justify-end">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Submit TOR</button>
    </div>
  </form>
</div>