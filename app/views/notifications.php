<?php
require_once __DIR__ . '/../notify.php';
if (!current_user()) {
    echo '<div class="bg-white p-6 rounded shadow">Silakan login untuk melihat notifikasi.</div>';
    return;
}
$user = current_user();
$notes = get_notifications($user['id'], false);
// ketika membuka halaman, tandai semua sebagai dibaca
mark_notifications_read($user['id']);
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Notifikasi</h2>
  <?php if(empty($notes)): ?>
    <div class="text-gray-600">Tidak ada notifikasi.</div>
  <?php else: ?>
    <ul class="space-y-2">
      <?php foreach($notes as $n): ?>
        <li class="border p-2 rounded">
          <div class="text-sm text-gray-600"><?= e($n['created_at']) ?></div>
          <div><?= e($n['message']) ?> <?php if($n['link']): ?><a class="text-blue-600" href="<?= BASE_URL ?>/?<?= ltrim($n['link'], '?') ?>">(lihat)</a><?php endif; ?></div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
