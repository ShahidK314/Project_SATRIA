<?php
if (!has_role('admin')) {
    echo '<div class="bg-white p-6 rounded shadow">Akses ditolak.</div>';
    return;
}
$pdo = get_pdo();
$stmt = $pdo->query('SELECT id, name, username, role, unit, created_at FROM users ORDER BY id');
$users = $stmt->fetchAll();
$success = flash_get('success');
$error = flash_get('error');
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Manajemen User (Admin)</h2>
  <?php if($success): ?><div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= e($success) ?></div><?php endif; ?>
  <?php if($error): ?><div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= e($error) ?></div><?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <h3 class="font-semibold mb-2">Buat User Baru</h3>
      <form method="post" action="<?= BASE_URL ?>/?page=admin_users">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="admin_create_user">
        <label class="block mb-2">Nama
          <input name="name" class="w-full border p-2 rounded" required>
        </label>
        <label class="block mb-2">Username
          <input name="username" class="w-full border p-2 rounded" required>
        </label>
        <label class="block mb-2">Password
          <input type="password" name="password" class="w-full border p-2 rounded" required>
        </label>
        <label class="block mb-2">Role
          <select name="role" class="w-full border p-2 rounded">
            <option value="pengusul">Pengusul</option>
            <option value="verifikator">Verifikator</option>
            <option value="ppk">PPK</option>
            <option value="wd2">WD2</option>
            <option value="bendahara">Bendahara</option>
            <option value="admin">Admin</option>
          </select>
        </label>
        <label class="block mb-2">Unit
          <input name="unit" class="w-full border p-2 rounded">
        </label>
        <div class="flex justify-end">
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Buat User</button>
        </div>
      </form>
    </div>
    <div>
      <h3 class="font-semibold mb-2">Daftar Pengguna</h3>
      <table class="w-full text-sm">
        <thead><tr class="bg-gray-100"><th class="p-2">ID</th><th class="p-2">Nama</th><th class="p-2">Username</th><th class="p-2">Role</th><th class="p-2">Unit</th></tr></thead>
        <tbody>
          <?php foreach($users as $u): ?>
            <tr class="border-t"><td class="p-2"><?= e($u['id']) ?></td><td class="p-2"><?= e($u['name']) ?></td><td class="p-2"><?= e($u['username']) ?></td><td class="p-2"><?= e($u['role']) ?></td><td class="p-2"><?= e($u['unit']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
