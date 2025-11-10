<?php
$pdo = get_pdo();
$user = current_user();

// Ambil TOR milik pengusul untuk referensi
$stmt = $pdo->prepare('SELECT id, judul FROM tor WHERE pengusul_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$tors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  $tor_id = intval($_POST['tor_id'] ?? 0);
  $rincian = $_POST['rincian'] ?? '';
  $total = floatval($_POST['total_realisasi'] ?? 0);

    // handle file uploads bukti dengan validasi
    $attachments = [];
    if (!empty($_FILES['bukti_files']) && is_array($_FILES['bukti_files']['name'])) {
        for ($i = 0; $i < count($_FILES['bukti_files']['name']); $i++) {
            if ($_FILES['bukti_files']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['bukti_files']['tmp_name'][$i];
                $name = basename($_FILES['bukti_files']['name'][$i]);
                $saved = save_upload($tmp, $name);
                if ($saved) $attachments[] = $saved;
            }
        }
    }
    $bukti_json = json_encode($attachments);

  $stmt = $pdo->prepare('INSERT INTO lpj (tor_id, pengusul_id, rincian, total_realisasi, bukti, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
  $stmt->execute([$tor_id, $user['id'], $rincian, $total, $bukti_json, 'submitted']);
    flash_set('success', 'LPJ berhasil disubmit.');
$header = header('Location: ' . BASE_URL . '/?page=dashboard');
  exit;
}
$success = flash_get('success');
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Buat LPJ (Laporan Pertanggungjawaban)</h2>
  <?php if($success): ?>
    <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= e($success) ?></div>
  <?php endif; ?>
  <form method="post" action="" class="space-y-4" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label>Realisasi untuk TOR
      <select name="tor_id" class="w-full border p-2 rounded" required>
        <option value="">-- Pilih TOR --</option>
        <?php foreach($tors as $t): ?>
          <option value="<?= e($t['id']) ?>"><?= e($t['id']) ?> - <?= e($t['judul']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Rincian Realisasi (JSON sederhana)
      <textarea name="rincian" class="w-full border p-2 rounded" placeholder='[{"item":"Konsumsi","qty":50,"harga":20000}]'></textarea>
    </label>
    <label>Total Realisasi
      <input name="total_realisasi" type="number" step="0.01" class="w-full border p-2 rounded" required>
    </label>
    <label>Bukti (unggah file, boleh lebih dari 1)
      <input type="file" name="bukti_files[]" multiple class="w-full">
    </label>
    <div class="flex justify-end">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Submit LPJ</button>
    </div>
  </form>
</div>