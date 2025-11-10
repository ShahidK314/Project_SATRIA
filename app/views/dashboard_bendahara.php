<?php
if (!has_role('bendahara')) {
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

// Ambil statistik
$stats = [
    'tor_ready' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status IN ('approved','funds_ready')")->fetchColumn(),
    'tor_released' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'funds_ready'")->fetchColumn(),
    'lpj_pending' => $pdo->query("SELECT COUNT(*) FROM lpj WHERE status = 'submitted'")->fetchColumn(),
    'lpj_completed' => $pdo->query("SELECT COUNT(*) FROM lpj WHERE status = 'completed'")->fetchColumn(),
    'notifications' => count(get_notifications($user['id'], true))
];

// Ambil data TOR yang siap dana
$pendingTORs = $pdo->query("
    SELECT t.*, u.name as pengusul_name, u.unit as unit_name
    FROM tor t 
    JOIN users u ON t.pengusul_id = u.id 
    WHERE t.status = 'approved'
    ORDER BY t.created_at DESC 
    LIMIT 5
")->fetchAll();

// Ambil data LPJ yang menunggu pembayaran
$pendingLPJs = $pdo->query("
    SELECT l.*, t.judul as tor_judul, u.name as pengusul_name
    FROM lpj l
    JOIN tor t ON l.tor_id = t.id
    JOIN users u ON t.pengusul_id = u.id
    WHERE l.status = 'submitted'
    ORDER BY l.created_at DESC
    LIMIT 5
")->fetchAll();

?>
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Dashboard Bendahara</h1>
      <p class="text-gray-700 mt-2">Selamat datang, <strong><?= e($user['name']) ?></strong>.</p>
    </div>
    <?php if($stats['notifications'] > 0): ?>
    <a href="<?= BASE_URL ?>/?page=notifications" class="flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
      </svg>
      <?= $stats['notifications'] ?> Notifikasi
    </a>
    <?php endif; ?>
  </div>

  <!-- Status Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <!-- TOR Siap Dana -->
    <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-indigo-800">TOR Siap Dana</p>
          <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $stats['tor_ready'] ?></p>
        </div>
        <div class="p-3 bg-indigo-200 rounded-full">
          <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Dana Dikeluarkan -->
    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-green-800">Dana Dikeluarkan</p>
          <p class="text-3xl font-bold text-green-600 mt-2"><?= $stats['tor_released'] ?></p>
        </div>
        <div class="p-3 bg-green-200 rounded-full">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- LPJ Menunggu -->
    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-yellow-800">LPJ Pending</p>
          <p class="text-3xl font-bold text-yellow-600 mt-2"><?= $stats['lpj_pending'] ?></p>
        </div>
        <div class="p-3 bg-yellow-200 rounded-full">
          <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- LPJ Selesai -->
    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-blue-800">LPJ Selesai</p>
          <p class="text-3xl font-bold text-blue-600 mt-2"><?= $stats['lpj_completed'] ?></p>
        </div>
        <div class="p-3 bg-blue-200 rounded-full">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- TOR Siap Dana Table -->
    <div class="bg-white rounded-lg border p-6">
      <h2 class="text-lg font-semibold mb-4">TOR Menunggu Pencairan</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($pendingTORs as $tor): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap text-sm">#<?= $tor['id'] ?></td>
              <td class="px-4 py-3 whitespace-nowrap text-sm"><?= e($tor['unit_name']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap text-sm">
                Rp <?= number_format($tor['total_anggaran'], 0, ',', '.') ?>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                <a href="<?= BASE_URL ?>/?page=tor_view&id=<?= $tor['id'] ?>" 
                   class="text-blue-600 hover:text-blue-900">Detail →</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($pendingTORs) == 0): ?>
            <tr>
              <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                Tidak ada TOR yang menunggu pencairan
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- LPJ Pending Table -->
    <div class="bg-white rounded-lg border p-6">
      <h2 class="text-lg font-semibold mb-4">LPJ Menunggu Verifikasi</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TOR</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengusul</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($pendingLPJs as $lpj): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap text-sm">#<?= $lpj['id'] ?></td>
              <td class="px-4 py-3 text-sm"><?= e($lpj['tor_judul']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap text-sm"><?= e($lpj['pengusul_name']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                <a href="<?= BASE_URL ?>/?page=lpj_view&id=<?= $lpj['id'] ?>" 
                   class="text-blue-600 hover:text-blue-900">Verifikasi →</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($pendingLPJs) == 0): ?>
            <tr>
              <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                Tidak ada LPJ yang menunggu verifikasi
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
