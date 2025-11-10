<?php
if (!has_role('pengusul')) {
    header('Location: ' . BASE_URL . '/?page=dashboard');
    exit;
}

$user = current_user();
$pdo = get_pdo();

function dash_card($title, $value, $link = '#', $color = 'blue') {
    return "<a href='$link' class='block p-4 rounded shadow-sm bg-{$color}-50 hover:bg-{$color}-100'>" .
           "<div class='text-sm text-gray-600'>" . htmlentities($title) . "</div>" .
           "<div class='text-2xl font-semibold mt-2'>" . htmlentities($value) . "</div></a>";
}

?>
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Dashboard Pengusul</h1>
  <p class="text-gray-700 mb-6">Selamat datang, <strong><?= e($user['name']) ?></strong>.</p>

  <!-- Kartu Aksi Cepat -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="<?= BASE_URL ?>/?page=tor_create" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
      <div class="p-3 bg-blue-500 text-white rounded-full mr-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
      </div>
      <div>
        <h3 class="font-semibold text-gray-900">Buat TOR Baru</h3>
        <p class="text-sm text-gray-600">Ajukan TOR baru</p>
      </div>
    </a>
    
    <a href="<?= BASE_URL ?>/?page=tor_list" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100">
      <div class="p-3 bg-green-500 text-white rounded-full mr-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
      </div>
      <div>
        <h3 class="font-semibold text-gray-900">Daftar TOR</h3>
        <p class="text-sm text-gray-600">Lihat semua TOR Anda</p>
      </div>
    </a>

    <a href="<?= BASE_URL ?>/?page=lpj_create" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100">
      <div class="p-3 bg-yellow-500 text-white rounded-full mr-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <div>
        <h3 class="font-semibold text-gray-900">Buat LPJ</h3>
        <p class="text-sm text-gray-600">Submit LPJ untuk TOR</p>
      </div>
    </a>
  </div>

  <!-- Status TOR -->
  <h2 class="text-lg font-semibold mb-4">Status TOR Anda</h2>
  <?php
    $s = $pdo->prepare('SELECT status, COUNT(*) as c FROM tor WHERE pengusul_id = ? GROUP BY status');
    $s->execute([$user['id']]);
    $rows = $s->fetchAll();
    $map = [];
    foreach($rows as $r) $map[$r['status']] = $r['c'];

    // Hitung total
    $total = array_sum($map);
  ?>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <?= dash_card('TOR Draft', $map['draft'] ?? 0, BASE_URL.'/?page=tor_list', 'gray') ?>
    <?= dash_card('Menunggu Review', $map['submitted'] ?? 0, BASE_URL.'/?page=tor_list', 'indigo') ?>
    <?= dash_card('Perlu Revisi', $map['needs_revision'] ?? 0, BASE_URL.'/?page=tor_list', 'red') ?>
    <?= dash_card('Disetujui', $map['approved'] ?? 0, BASE_URL.'/?page=tor_list', 'green') ?>
  </div>

  <!-- Statistik -->
  <div class="bg-gray-50 rounded-lg p-6">
    <h2 class="text-lg font-semibold mb-4">Statistik TOR</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="text-center">
        <div class="text-3xl font-bold text-blue-600"><?= $total ?></div>
        <div class="text-sm text-gray-600">Total TOR</div>
      </div>
      <div class="text-center">
        <div class="text-3xl font-bold text-green-600"><?= $map['approved'] ?? 0 ?></div>
        <div class="text-sm text-gray-600">Disetujui</div>
      </div>
      <div class="text-center">
        <div class="text-3xl font-bold text-yellow-600"><?= $map['submitted'] ?? 0 ?></div>
        <div class="text-sm text-gray-600">Dalam Review</div>
      </div>
      <div class="text-center">
        <div class="text-3xl font-bold text-red-600"><?= $map['rejected'] ?? 0 ?></div>
        <div class="text-sm text-gray-600">Ditolak</div>
      </div>
    </div>
  </div>
</div>
