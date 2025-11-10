<?php
$error = flash_get('error');
$success = flash_get('success');
$old = $_SESSION['old'] ?? [];
?>
<div class="max-w-xl mx-auto">
  <style>
    /* Improve reCAPTCHA layout: center and scale responsively */
    .recaptcha-row { display:flex; justify-content:center; margin-top:0.5rem; margin-bottom:0.5rem; }
    .recaptcha-wrapper { width: 320px; max-width:100%; display:flex; justify-content:center; }
    /* Scale the widget slightly on small screens to avoid overflow */
    .recaptcha-wrapper .g-recaptcha { transform-origin: 0 0; }
    @media (max-width: 420px) {
      .recaptcha-wrapper { width: 260px; }
      .recaptcha-wrapper .g-recaptcha { transform: scale(0.85); }
    }
    @media (min-width: 421px) and (max-width: 768px) {
      .recaptcha-wrapper { width: 300px; }
      .recaptcha-wrapper .g-recaptcha { transform: scale(0.95); }
    }
    /* Hide Google's test warning text (dev-only). Targets the common test-warning classes used by reCAPTCHA v2. */
    .rc-anchor .rc-anchor-test-warning,
    .rc-anchor-test-warning {
      display: none !important;
      visibility: hidden !important;
      height: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
    }
  </style>
  <div class="bg-white p-6 rounded shadow card-emboss">
    <h2 class="text-2xl font-semibold mb-2">Daftar Pengusul</h2>
    <p class="text-sm text-gray-600 mb-4">Buat akun pengusul untuk mengajukan TOR. Mohon isi data dengan lengkap.</p>

    <?php if($error): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/?page=register_pengusul" class="space-y-4">
      <?= csrf_field() ?>

      <label class="block">
        <span class="text-sm text-gray-700">Nama Lengkap</span>
        <input name="name" value="<?= e($old['name'] ?? '') ?>" required class="mt-1 block w-full rounded border px-3 py-2" placeholder="Nama lengkap sesuai identitas">
      </label>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-700">Username</span>
          <input name="username" value="<?= e($old['username'] ?? '') ?>" required minlength="4" class="mt-1 block w-full rounded border px-3 py-2" placeholder="username unik">
        </label>
        <label class="block">
          <span class="text-sm text-gray-700">Email</span>
          <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required class="mt-1 block w-full rounded border px-3 py-2" placeholder="nama@domain.tld">
        </label>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-700">No. HP</span>
          <input name="phone" value="<?= e($old['phone'] ?? '') ?>" pattern="[0-9+\s-]{6,20}" class="mt-1 block w-full rounded border px-3 py-2" placeholder="0812xxxx">
        </label>
        <label class="block">
          <span class="text-sm text-gray-700">Unit / Departemen</span>
          <input name="department" value="<?= e($old['department'] ?? '') ?>" class="mt-1 block w-full rounded border px-3 py-2" placeholder="Contoh: Fakultas Teknik">
        </label>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm text-gray-700">Kata Sandi</span>
          <input type="password" name="password" required minlength="8" class="mt-1 block w-full rounded border px-3 py-2" placeholder="Minimal 8 karakter">
        </label>
        <label class="block">
          <span class="text-sm text-gray-700">Konfirmasi Kata Sandi</span>
          <input type="password" name="password_confirm" required minlength="8" class="mt-1 block w-full rounded border px-3 py-2" placeholder="Ketik ulang kata sandi">
        </label>
      </div>

      <?php if(defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY && RECAPTCHA_SITE_KEY !== 'your-site-key'): ?>
        <div class="recaptcha-row" aria-hidden="false">
          <div class="recaptcha-wrapper">
            <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
          </div>
        </div>
      <?php else: ?>
        <!-- reCAPTCHA tidak dikonfigurasi. Untuk produksi, atur RECAPTCHA_SITE_KEY di app/config.php -->
      <?php endif; ?>

      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">Dengan mendaftar Anda menyetujui kebijakan penggunaan.</div>
        <div class="flex items-center space-x-3">
          <a href="<?= BASE_URL ?>/?page=login" class="text-sm text-gray-600 hover:text-gray-800">Sudah punya akun? Masuk</a>
          <button class="btn-azure" type="submit">Daftar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if(defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY && RECAPTCHA_SITE_KEY !== 'your-site-key'): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
// Clear old input after rendering
unset($_SESSION['old']);
?>
