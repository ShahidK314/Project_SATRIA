<?php
$pdo = get_pdo();
$user = current_user();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT t.*, u.name as pengusul_name FROM tor t JOIN users u ON t.pengusul_id = u.id WHERE t.id = ?');
$stmt->execute([$id]);
$t = $stmt->fetch();
if (!$t) {
    echo '<div class="bg-white p-6 rounded shadow">TOR tidak ditemukan.</div>';
    return;
}

// attachments
$attachments = [];
if (!empty($t['attachments'])) {
    $attachments = json_decode($t['attachments'], true) ?: [];
}

?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-2">Detail TOR: <?= e($t['judul']) ?></h2>
  <div class="text-sm text-gray-600 mb-4">Pengusul: <?= e($t['pengusul_name']) ?> | Status: <strong><?= e($t['status']) ?></strong></div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <p><strong>Tanggal:</strong> <?= e($t['tanggal_mulai']) ?> - <?= e($t['tanggal_selesai']) ?></p>
      <p><strong>Lokasi:</strong> <?= e($t['lokasi']) ?></p>
      <p class="mt-2"><strong>Deskripsi:</strong><br><?= nl2br(e($t['deskripsi'])) ?></p>
    </div>
    <div>
      <p><strong>Total Anggaran:</strong> Rp <?= number_format($t['total_anggaran'],0,',','.') ?></p>
      <p class="mt-2"><strong>RAB:</strong></p>
      <pre class="bg-gray-50 p-2 rounded text-sm"><?= e($t['rab']) ?></pre>
      <?php if($attachments): ?>
        <p class="mt-2"><strong>Lampiran:</strong></p>
        <ul class="list-disc ml-6">
          <?php foreach($attachments as $a): ?>
            <li><a class="text-blue-600" href="<?= BASE_URL ?>/uploads/<?= e($a) ?>" target="_blank"><?= e($a) ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="mt-6">
    <?php if(has_role('verifikator') && $t['status'] === 'submitted'): ?>
      <form method="post" action="<?= BASE_URL ?>/?page=tor_view&id=<?= e($t['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="tor_action">
        <input type="hidden" name="id" value="<?= e($t['id']) ?>">
        <div class="mb-2">
          <label class="block text-sm">Komentar (wajib jika menolak atau minta revisi)</label>
          <textarea name="comment" class="w-full border p-2 rounded"></textarea>
        </div>
        <button name="op" value="approve" class="px-3 py-2 bg-green-600 text-white rounded">Verifikasi</button>
        <button name="op" value="request_revision" class="px-3 py-2 bg-yellow-500 text-white rounded">Minta Revisi</button>
        <button name="op" value="reject" class="px-3 py-2 bg-red-600 text-white rounded">Tolak</button>
      </form>
    <?php endif; ?>

    <?php if(has_role('ppk') && in_array($t['status'], ['verified','submitted','needs_revision'])): ?>
      <form method="post" action="<?= BASE_URL ?>/?page=tor_view&id=<?= e($t['id']) ?>" class="mt-2">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="tor_action">
        <input type="hidden" name="id" value="<?= e($t['id']) ?>">
        <div class="mb-2">
          <label class="block text-sm">Komentar (wajib jika menolak)</label>
          <textarea name="comment" class="w-full border p-2 rounded"></textarea>
        </div>
        <button name="op" value="approve" class="px-3 py-2 bg-green-600 text-white rounded">Setujui (PPK)</button>
        <button name="op" value="reject" class="px-3 py-2 bg-red-600 text-white rounded">Tolak</button>
      </form>
    <?php endif; ?>

    <?php if(has_role('wd2') && $t['status'] === 'pending_wd2'): ?>
      <form method="post" action="<?= BASE_URL ?>/?page=tor_view&id=<?= e($t['id']) ?>" class="mt-2">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="tor_action">
        <input type="hidden" name="id" value="<?= e($t['id']) ?>">
        <div class="mb-2">
          <label class="block text-sm">Komentar (wajib jika menolak)</label>
          <textarea name="comment" class="w-full border p-2 rounded"></textarea>
        </div>
        <button name="op" value="approve" class="px-3 py-2 bg-green-600 text-white rounded">Setujui (WD2)</button>
        <button name="op" value="reject" class="px-3 py-2 bg-red-600 text-white rounded">Tolak</button>
      </form>
    <?php endif; ?>

    <?php if(has_role('bendahara') && $t['status'] === 'approved'): ?>
      <form method="post" action="<?= BASE_URL ?>/?page=tor_view&id=<?= e($t['id']) ?>" class="mt-2">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="tor_action">
        <input type="hidden" name="id" value="<?= e($t['id']) ?>">
        <div class="mb-2">
          <label class="block text-sm">Catatan Bendahara (opsional)</label>
          <textarea name="comment" class="w-full border p-2 rounded"></textarea>
        </div>
        <button name="op" value="approve" class="px-3 py-2 bg-indigo-600 text-white rounded">Siapkan Dana (Bendahara)</button>
      </form>
    <?php endif; ?>
  </div>

  <?php
    // tampilkan audit
    $logs = get_pdo()->prepare('SELECT a.*, u.name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE a.entity_type = "tor" AND a.entity_id = ? ORDER BY a.created_at DESC');
    $logs->execute([$t['id']]);
    $alogs = $logs->fetchAll();
  ?>
  <?php if($alogs): ?>
    <div class="mt-6">
      <h3 class="font-semibold">Audit Trail</h3>
      <ul class="text-sm text-gray-700 mt-2">
        <?php foreach($alogs as $l): ?>
          <li><?= e($l['created_at']) ?> — <?= e($l['name'] ?? 'System') ?> — <?= e($l['action']) ?> <?= $l['comment'] ? ' — ' . e($l['comment']) : '' ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

</div>
