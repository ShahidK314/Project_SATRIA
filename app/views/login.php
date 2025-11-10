<?php
$error = flash_get('error');
?>
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-semibold mb-4">Login SATRIA</h2>
  <?php if($error): ?>
    <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="post" action="<?= BASE_URL ?>/?page=login">
    <?= csrf_field() ?>
    <label class="block mb-2">Username
      <input name="username" class="w-full border p-2 rounded" required>
    </label>
    <label class="block mb-4">Kata Sandi
      <input type="password" name="password" class="w-full border p-2 rounded" required>
    </label>
    <div class="flex justify-end">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Masuk</button>
    </div>
  </form>
    <div class="mt-4 text-sm text-gray-600">
      Belum punya akun? <a href="<?= BASE_URL ?>/?page=register_pengusul" class="text-blue-700 hover:underline">Daftar sebagai Pengusul</a>
    </div>
</div>