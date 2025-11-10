<?php
if (!has_role('verifikator')) {
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

// Ambil statistik TOR & LPJ
$stats = [
    'tor_pending' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'submitted'")->fetchColumn(),
    'tor_verified' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'verified'")->fetchColumn(),
    'tor_total' => $pdo->query("SELECT COUNT(*) FROM tor")->fetchColumn(),
    'lpj_pending' => $pdo->query("SELECT COUNT(*) FROM lpj WHERE status = 'submitted'")->fetchColumn(),
    'lpj_verified' => $pdo->query("SELECT COUNT(*) FROM lpj WHERE status = 'verified'")->fetchColumn(),
    'lpj_total' => $pdo->query("SELECT COUNT(*) FROM lpj")->fetchColumn()
];

?>
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Dashboard Verifikator</h1>
  <p class="text-gray-700 mb-6">Selamat datang, <strong><?= e($user['name']) ?></strong>. Anda memiliki <?= $stats['tor_pending'] ?> TOR dan <?= $stats['lpj_pending'] ?> LPJ yang memerlukan verifikasi.</p>

  <!-- Kartu Tugas Utama -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- TOR Panel -->
    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg font-semibold text-yellow-800">TOR Menunggu Verifikasi</h2>
        <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm font-semibold">
          <?= $stats['tor_pending'] ?> TOR
        </span>
      </div>
      <p class="text-sm text-yellow-700 mb-4">TOR yang perlu diperiksa untuk kelengkapan dan kesesuaian</p>
      <a href="<?= BASE_URL ?>/?page=tor_list" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Periksa TOR
      </a>
    </div>

    <!-- LPJ Panel -->
    <div class="bg-orange-50 p-6 rounded-lg border border-orange-200">
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg font-semibold text-orange-800">LPJ Menunggu Verifikasi</h2>
        <span class="px-3 py-1 bg-orange-200 text-orange-800 rounded-full text-sm font-semibold">
          <?= $stats['lpj_pending'] ?> LPJ
        </span>
      </div>
      <p class="text-sm text-orange-700 mb-4">LPJ yang perlu diperiksa untuk bukti dan laporan</p>
      <a href="<?= BASE_URL ?>/?page=lpj_list" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Periksa LPJ
      </a>
    </div>
  </div>

  <!-- Statistik -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg border shadow-sm">
      <div class="text-sm text-gray-600">Total TOR</div>
      <div class="text-2xl font-bold text-gray-800"><?= $stats['tor_total'] ?></div>
    </div>
    <div class="bg-white p-4 rounded-lg border shadow-sm">
      <div class="text-sm text-gray-600">TOR Terverifikasi</div>
      <div class="text-2xl font-bold text-green-600"><?= $stats['tor_verified'] ?></div>
    </div>
    <div class="bg-white p-4 rounded-lg border shadow-sm">
      <div class="text-sm text-gray-600">Total LPJ</div>
      <div class="text-2xl font-bold text-gray-800"><?= $stats['lpj_total'] ?></div>
    </div>
    <div class="bg-white p-4 rounded-lg border shadow-sm">
      <div class="text-sm text-gray-600">LPJ Terverifikasi</div>
      <div class="text-2xl font-bold text-green-600"><?= $stats['lpj_verified'] ?></div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="bg-gray-50 rounded-lg p-6">
    <h2 class="text-lg font-semibold mb-4">Aktivitas Terbaru</h2>
    <?php
    $recentActivity = $pdo->query("
        SELECT 'tor' as type, id, status, updated_at 
        FROM tor 
        WHERE status IN ('submitted', 'verified') 
        UNION ALL
        SELECT 'lpj' as type, id, status, updated_at 
        FROM lpj 
        WHERE status IN ('submitted', 'verified')
        ORDER BY updated_at DESC LIMIT 5
    ")->fetchAll();
    ?>
    <div class="space-y-3">
      <?php foreach ($recentActivity as $activity): ?>
        <div class="flex items-center justify-between p-3 bg-white rounded border">
          <div>
            <span class="font-medium"><?= strtoupper($activity['type']) ?> #<?= $activity['id'] ?></span>
            <span class="text-sm text-gray-600 ml-2"><?= $activity['status'] ?></span>
          </div>
          <a href="<?= BASE_URL ?>/?page=<?= $activity['type'] ?>_view&id=<?= $activity['id'] ?>" 
             class="text-blue-600 hover:text-blue-800 text-sm">
            Lihat Detail →
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
