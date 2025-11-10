<?php
if (!has_role('ppk')) {
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
    'pending_approval' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'verified'")->fetchColumn(),
    'high_value' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status = 'verified' AND total_anggaran > 50000000")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM tor WHERE status IN ('approved', 'pending_wd2')")->fetchColumn(),
    'total' => $pdo->query("SELECT COUNT(*) FROM tor")->fetchColumn(),
    'notifications' => count(get_notifications($user['id'], true))
];

// Ambil 5 TOR terbaru yang perlu persetujuan
$pendingTORs = $pdo->query("
    SELECT t.*, u.name as pengusul_name 
    FROM tor t 
    JOIN users u ON t.pengusul_id = u.id 
    WHERE t.status = 'verified' 
    ORDER BY t.created_at DESC 
    LIMIT 5
")->fetchAll();

?>
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Dashboard PPK</h1>
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

  <!-- Kartu Status -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
      <div class="flex items-center">
        <div class="p-3 bg-purple-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-purple-600">Menunggu Persetujuan</p>
          <p class="text-2xl font-bold text-purple-800"><?= $stats['pending_approval'] ?></p>
        </div>
      </div>
    </div>

    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
      <div class="flex items-center">
        <div class="p-3 bg-red-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-red-600">Nilai > 50jt</p>
          <p class="text-2xl font-bold text-red-800"><?= $stats['high_value'] ?></p>
        </div>
      </div>
    </div>

    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
      <div class="flex items-center">
        <div class="p-3 bg-green-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-green-600">Disetujui</p>
          <p class="text-2xl font-bold text-green-800"><?= $stats['approved'] ?></p>
        </div>
      </div>
    </div>

    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
      <div class="flex items-center">
        <div class="p-3 bg-blue-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-blue-600">Total TOR</p>
          <p class="text-2xl font-bold text-blue-800"><?= $stats['total'] ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Daftar TOR Menunggu -->
  <div class="bg-white rounded-lg border p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">TOR Menunggu Persetujuan</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="border-b bg-gray-50">
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengusul</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pendingTORs as $tor): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm">#<?= $tor['id'] ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= e($tor['pengusul_name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= date('d/m/Y', strtotime($tor['created_at'])) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">Rp <?= number_format($tor['total_anggaran'], 0, ',', '.') ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <a href="<?= BASE_URL ?>/?page=tor_view&id=<?= $tor['id'] ?>" 
                 class="text-blue-600 hover:text-blue-900">Tinjau →</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if(count($pendingTORs) == 0): ?>
        <p class="text-center text-gray-500 py-4">Tidak ada TOR yang menunggu persetujuan</p>
      <?php endif; ?>
    </div>
    <?php if($stats['pending_approval'] > 5): ?>
      <div class="mt-4 text-center">
        <a href="<?= BASE_URL ?>/?page=tor_list" class="text-blue-600 hover:text-blue-900">
          Lihat <?= $stats['pending_approval'] - 5 ?> TOR lainnya →
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
