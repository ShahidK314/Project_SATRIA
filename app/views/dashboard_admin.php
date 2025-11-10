<?php
if (!has_role('admin')) {
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

// Ambil statistik sistem
$stats = [
    'users_total' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'tor_total' => $pdo->query('SELECT COUNT(*) FROM tor')->fetchColumn(),
    'lpj_total' => $pdo->query('SELECT COUNT(*) FROM lpj')->fetchColumn(),
    'tor_this_month' => $pdo->query('SELECT COUNT(*) FROM tor WHERE MONTH(created_at) = MONTH(CURRENT_DATE())')->fetchColumn(),
    'active_users' => $pdo->query('SELECT COUNT(DISTINCT pengusul_id) FROM tor WHERE MONTH(created_at) = MONTH(CURRENT_DATE())')->fetchColumn()
];

// Ambil statistik per role
$roleStats = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll();

// Ambil pengguna terbaru
$recentUsers = $pdo->query("
    SELECT * FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

// Ambil aktivitas sistem terbaru
$recentActivity = $pdo->query("
    SELECT 'tor' as type, id, status, created_at, pengusul_id
    FROM tor 
    UNION ALL
    SELECT 'lpj' as type, id, status, created_at, NULL as pengusul_id
    FROM lpj
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();

?>
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Dashboard Administrator</h1>
      <p class="text-gray-700 mt-2">Selamat datang, <strong><?= e($user['name']) ?></strong>.</p>
    </div>
    <a href="<?= BASE_URL ?>/?page=admin_users" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Tambah User Baru
    </a>
  </div>

  <!-- Main Stats -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <!-- Users Card -->
    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
      <div class="flex items-center">
        <div class="p-3 bg-blue-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-blue-600">Total Pengguna</p>
          <p class="text-2xl font-bold text-blue-800"><?= $stats['users_total'] ?></p>
        </div>
      </div>
      <div class="mt-4 text-sm text-blue-600">
        <?= $stats['active_users'] ?> pengguna aktif bulan ini
      </div>
    </div>

    <!-- TOR Card -->
    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
      <div class="flex items-center">
        <div class="p-3 bg-green-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-green-600">Total TOR</p>
          <p class="text-2xl font-bold text-green-800"><?= $stats['tor_total'] ?></p>
        </div>
      </div>
      <div class="mt-4 text-sm text-green-600">
        <?= $stats['tor_this_month'] ?> TOR baru bulan ini
      </div>
    </div>

    <!-- LPJ Card -->
    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
      <div class="flex items-center">
        <div class="p-3 bg-purple-500 rounded-full mr-4">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm text-purple-600">Total LPJ</p>
          <p class="text-2xl font-bold text-purple-800"><?= $stats['lpj_total'] ?></p>
        </div>
      </div>
      <div class="mt-4 text-sm text-purple-600">
        <?= round(($stats['lpj_total'] / ($stats['tor_total'] ?: 1)) * 100) ?>% dari total TOR
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
      <div class="space-y-2">
        <a href="<?= BASE_URL ?>/?page=admin_users" class="block text-sm text-blue-600 hover:text-blue-800">
          → Manajemen User
        </a>
        <a href="<?= BASE_URL ?>/?page=tor_list" class="block text-sm text-blue-600 hover:text-blue-800">
          → Lihat Semua TOR
        </a>
        <a href="<?= BASE_URL ?>/?page=lpj_list" class="block text-sm text-blue-600 hover:text-blue-800">
          → Lihat Semua LPJ
        </a>
      </div>
    </div>
  </div>

  <!-- User Distribution -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- User by Role -->
    <div class="bg-white rounded-lg border p-6">
      <h2 class="text-lg font-semibold mb-4">Distribusi Pengguna</h2>
      <div class="space-y-4">
        <?php foreach($roleStats as $role): ?>
          <div class="flex items-center">
            <div class="w-32 text-sm text-gray-600"><?= ucfirst($role['role']) ?></div>
            <div class="flex-1">
              <div class="h-4 bg-gray-100 rounded-full">
                <?php $percentage = ($role['count'] / $stats['users_total']) * 100; ?>
                <div class="h-4 bg-blue-500 rounded-full" style="width: <?= $percentage ?>%"></div>
              </div>
            </div>
            <div class="w-16 text-right text-sm text-gray-600"><?= $role['count'] ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg border p-6">
      <h2 class="text-lg font-semibold mb-4">Pengguna Terbaru</h2>
      <div class="space-y-3">
        <?php foreach($recentUsers as $u): ?>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
            <div>
              <span class="font-medium"><?= e($u['name']) ?></span>
              <span class="text-sm text-gray-600 ml-2">(<?= e($u['role']) ?>)</span>
            </div>
            <div class="text-sm text-gray-500">
              <?= date('d/m/Y', strtotime($u['created_at'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="mt-6 bg-white rounded-lg border p-6">
    <h2 class="text-lg font-semibold mb-4">Aktivitas Sistem Terbaru</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="border-b bg-gray-50">
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($recentActivity as $activity): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                  <?= $activity['type'] === 'tor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                  <?= strtoupper($activity['type']) ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                #<?= $activity['id'] ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                  <?php
                    $statusColor = match($activity['status']) {
                      'approved' => 'bg-green-100 text-green-800',
                      'rejected' => 'bg-red-100 text-red-800',
                      'pending_wd2' => 'bg-yellow-100 text-yellow-800',
                      default => 'bg-gray-100 text-gray-800'
                    };
                    echo $statusColor;
                  ?>">
                  <?= ucfirst($activity['status']) ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <a href="<?= BASE_URL ?>/?page=<?= $activity['type'] ?>_view&id=<?= $activity['id'] ?>" 
                   class="text-blue-600 hover:text-blue-900">
                  Lihat Detail →
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
