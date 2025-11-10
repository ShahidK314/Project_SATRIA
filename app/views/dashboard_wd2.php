<?php
if (!has_role('wd2')) {
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

// Ambil statistik TOR
$stats = [
    'pending_wd2' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'pending_wd2'")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'approved' AND total_anggaran > 50000000")->fetchColumn(),
    'total_high_value' => $pdo->query("SELECT COUNT(*) FROM tor WHERE total_anggaran > 50000000")->fetchColumn(),
    'notifications' => count(get_notifications($user['id'], true))
];

// Ambil TOR bernilai tinggi yang perlu ditinjau
$pendingTORs = $pdo->query("
    SELECT t.*, u.name as pengusul_name, u.unit as unit_name
    FROM tor t 
    JOIN users u ON t.pengusul_id = u.id 
    WHERE t.status = 'pending_wd2'
    ORDER BY t.total_anggaran DESC, t.created_at DESC 
    LIMIT 5
")->fetchAll();

?>
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Dashboard Wakil Dekan II</h1>
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

  <!-- Status Overview -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Pending Review Card -->
    <div class="bg-red-50 p-6 rounded-lg border border-red-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-red-800">TOR Menunggu Review</p>
          <p class="text-3xl font-bold text-red-600 mt-2"><?= $stats['pending_wd2'] ?></p>
        </div>
        <div class="p-3 bg-red-200 rounded-full">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
      </div>
      <p class="text-sm text-red-600 mt-2">TOR bernilai tinggi yang memerlukan persetujuan Anda</p>
    </div>

    <!-- Approved Card -->
    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-green-800">Disetujui</p>
          <p class="text-3xl font-bold text-green-600 mt-2"><?= $stats['approved'] ?></p>
        </div>
        <div class="p-3 bg-green-200 rounded-full">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
      <p class="text-sm text-green-600 mt-2">TOR bernilai tinggi yang telah disetujui</p>
    </div>

    <!-- Total High Value Card -->
    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-lg font-semibold text-blue-800">Total Nilai Tinggi</p>
          <p class="text-3xl font-bold text-blue-600 mt-2"><?= $stats['total_high_value'] ?></p>
        </div>
        <div class="p-3 bg-blue-200 rounded-full">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
      <p class="text-sm text-blue-600 mt-2">Total TOR dengan nilai di atas 50 juta</p>
    </div>
  </div>

  <!-- Pending TOR Table -->
  <div class="bg-white rounded-lg border p-6">
    <h2 class="text-lg font-semibold mb-4">TOR Menunggu Persetujuan WD2</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="border-b bg-gray-50">
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengusul</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pendingTORs as $tor): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm">#<?= $tor['id'] ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= e($tor['unit_name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= e($tor['pengusul_name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
              Rp <?= number_format($tor['total_anggaran'], 0, ',', '.') ?>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Menunggu WD2
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <a href="<?= BASE_URL ?>/?page=tor_view&id=<?= $tor['id'] ?>" 
                 class="text-blue-600 hover:text-blue-900">Review →</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(count($pendingTORs) == 0): ?>
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
              Tidak ada TOR yang menunggu persetujuan
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($stats['pending_wd2'] > 5): ?>
      <div class="mt-4 text-center">
        <a href="<?= BASE_URL ?>/?page=tor_list" class="text-blue-600 hover:text-blue-900">
          Lihat <?= $stats['pending_wd2'] - 5 ?> TOR lainnya →
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
