<?php
$user = current_user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SISTEM SATRIA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Background gradient: Azure blue -> White */
    :root{
      --azure-1: #0078d4; /* primary azure */
      --azure-2: #5fb3ff; /* lighter azure */
    }
    body.app-bg {
      background: linear-gradient(135deg, var(--azure-1) 0%, var(--azure-2) 40%, #ffffff 100%);
      background-attachment: fixed;
      /* provide a subtle overlay to keep content readable */
      color-scheme: light;
    }

    /* Nav glass with graceful fallback: use backdrop-filter when supported, otherwise use solid with slightly higher opacity */
    .nav-glass {
      background-color: rgba(255,255,255,0.92);
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    @supports ((-webkit-backdrop-filter: blur(6px)) or (backdrop-filter: blur(6px))) {
      .nav-glass {
        background-color: rgba(255,255,255,0.78);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
      }
    }

    /* Mobile: make nav more opaque to ensure contrast on small screens */
    @media (max-width: 640px) {
      .nav-glass { background-color: rgba(255,255,255,0.96); }
    }
    /* Header/link/button theming to match azure gradient */
    .brand { color: var(--azure-1); }
    .nav-link { color: #374151; text-decoration: none; }
    .nav-link:hover { color: var(--azure-1); }
    .btn-azure {
      background: var(--azure-1);
      color: #fff;
      padding: .45rem .75rem;
      border-radius: .375rem;
      display: inline-block;
    }
    .btn-azure:hover { filter: brightness(0.92); }
    /* Outline red button for Logout: subtle by default, filled on hover for affordance */
    .btn-red {
      background: transparent;
      color: #ef4444;
      padding: .45rem .75rem;
      border-radius: .375rem;
      border: 1px solid #ef4444;
      display: inline-block;
    }
    .btn-red:hover {
      background: #ef4444;
      color: #fff;
      filter: none;
    }
    .notif-bell { color: #374151; font-size: 1.05rem; }
    /* Card emboss / subtle shadow for better contrast on gradient background */
    /* Applies to content cards that use .bg-white within the main content area */
    main .bg-white {
      border-radius: .6rem;
      border: 1px solid rgba(2,6,23,0.04);
      box-shadow: 0 10px 25px rgba(2,6,23,0.06), inset 0 1px 0 rgba(255,255,255,0.6);
      background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(255,255,255,0.96));
    }
    /* a utility class for components that want a stronger embossed card */
    .card-emboss {
      border-radius: .6rem;
      border: 1px solid rgba(2,6,23,0.06);
      box-shadow: 0 14px 40px rgba(2,6,23,0.08), inset 0 1px 0 rgba(255,255,255,0.6);
      background: linear-gradient(180deg, rgba(255,255,255,0.99), rgba(250,250,250,0.98));
    }
  </style>
</head>
<body class="app-bg min-h-screen">
<nav class="bg-white shadow">
  <div class="max-w-7xl mx-auto px-4 nav-glass">
    <div class="flex justify-between h-16 items-center">
      <div class="flex items-center">
        <a class="text-xl font-semibold brand" href="<?= BASE_URL ?>/?page=dashboard">SATRIA</a>
        <?php if($user): ?>
          <!-- Menu untuk Pengusul -->
          <?php if(has_role('pengusul')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=tor_create">Buat TOR</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=tor_list">Daftar TOR</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=lpj_create">Buat LPJ</a>
          <?php endif; ?>

          <!-- Menu untuk Verifikator -->
          <?php if(has_role('verifikator')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=tor_list">TOR Pending</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=lpj_list">LPJ Pending</a>
          <?php endif; ?>

          <!-- Menu untuk PPK -->
          <?php if(has_role('ppk')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=tor_list">TOR Menunggu</a>
          <?php endif; ?>

          <!-- Menu untuk WD2 -->
          <?php if(has_role('wd2')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=tor_list">TOR > 50jt</a>
          <?php endif; ?>

          <!-- Menu untuk Bendahara -->
          <?php if(has_role('bendahara')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=tor_list">TOR Approved</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=lpj_list">LPJ Pending</a>
          <?php endif; ?>

          <!-- Menu untuk Admin -->
          <?php if(has_role('admin')): ?>
            <a class="ml-6 nav-link" href="<?= BASE_URL ?>/?page=admin_users">Manajemen User</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=tor_list">Semua TOR</a>
            <a class="ml-4 nav-link" href="<?= BASE_URL ?>/?page=lpj_list">Semua LPJ</a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="flex items-center">
        <?php if($user): ?>
          <?php
            require_once __DIR__ . '/../notify.php';
            $notes = get_notifications($user['id'], true);
            $countNotes = count($notes);
          ?>
          <div class="relative mr-4">
            <a href="<?= BASE_URL ?>/?page=notifications" class="notif-bell">🔔</a>
            <?php if($countNotes > 0): ?>
              <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1"><?= $countNotes ?></span>
            <?php endif; ?>
          </div>
          <div class="text-sm mr-4">Halo, <strong><?= e($user['name']) ?></strong> (<?= e($user['role']) ?>)</div>
          <a class="btn-red" href="<?= BASE_URL ?>/?page=logout">Logout</a>
        <?php else: ?>
          <a class="btn-azure" href="<?= BASE_URL ?>/?page=login">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="max-w-5xl mx-auto mt-8 p-4">