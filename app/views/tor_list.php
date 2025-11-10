<?php
$pdo = get_pdo();
$user = current_user();

// Ambil TOR sesuai role
if (has_role('pengusul')) {
    $stmt = $pdo->prepare('SELECT t.*, u.name as pengusul_name FROM tor t JOIN users u ON t.pengusul_id = u.id WHERE t.pengusul_id = ? ORDER BY t.created_at DESC');
    $stmt->execute([$user['id']]);
} else {
    // role lain lihat semua untuk demo
    $stmt = $pdo->query('SELECT t.*, u.name as pengusul_name FROM tor t JOIN users u ON t.pengusul_id = u.id ORDER BY t.created_at DESC');
}
$tors = $stmt->fetchAll();
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Daftar TOR</h2>
  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="p-2 border">ID</th>
        <th class="p-2 border">Judul</th>
        <th class="p-2 border">Pengusul</th>
        <th class="p-2 border">Total</th>
        <th class="p-2 border">Status</th>
        <th class="p-2 border">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($tors as $t): ?>
      <tr>
        <td class="p-2 border"><?= e($t['id']) ?></td>
        <td class="p-2 border"><?= e($t['judul']) ?></td>
        <td class="p-2 border"><?= e($t['pengusul_name']) ?></td>
        <td class="p-2 border">Rp <?= number_format($t['total_anggaran'],0,',','.') ?></td>
        <td class="p-2 border"><?= e($t['status']) ?></td>
        <td class="p-2 border">
          <a class="text-blue-600" href="<?= BASE_URL ?>/?page=tor_view&id=<?= e($t['id']) ?>">Lihat</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>