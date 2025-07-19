<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$role = $_SESSION['role'] ?? null;
$tipe = $_SESSION['tipe_toko'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div
  class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 shadow-lg transform transition-transform duration-200 ease-in-out
         -translate-x-full sm:translate-x-0 sm:static sm:inset-0"
  :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
  <!-- Judul Sidebar -->
  <div class="px-6 py-5 border-b border-gray-100">
    <h2 class="text-xl font-semibold text-gray-800">InventoBox</h2>
  </div>

  <!-- Navigasi -->
  <nav class="px-4 py-4">
    <ul class="space-y-2 text-gray-700 text-sm">

      <?php if ($role === 'admin'): ?>
         <!-- Dashboard -->
      <li>
        <a href="/inventobox/pages/admin/dashboard.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition<?php if ($currentPage === 'dashboard.php') echo ' bg-gray-200 font-semibold'; ?>">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 10h4v10H3V10zm7-6h4v16h-4V4zm7 10h4v6h-4v-6z" />
          </svg>
          Dashboard
        </a>
      </li>
        <!-- 🔹 Manajemen Sistem -->
        <li class="mt-4 px-4 text-xs text-gray-400 uppercase">Manajemen Sistem</li>
        <li>
          <a href="/inventobox/pages/admin/manajemen_user.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition<?php if ($currentPage === 'manajemen_user.php') echo ' bg-gray-200 font-semibold'; ?>">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M5.121 17.804A4.992 4.992 0 0010 20a4.992 4.992 0 004.879-2.196M15 11a3 3 0 100-6 3 3 0 000 6zm-6 0a3 3 0 100-6 3 3 0 000 6z" />
            </svg>
            Manajemen User
          </a>
        </li>
        <!-- 🔹 Produk -->
        <li class="mt-4 px-4 text-xs text-gray-400 uppercase">Produk</li>
      <li>
        <a href="/inventobox/pages/admin/tambah_produk.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition<?php if ($currentPage === 'tambah_produk.php') echo ' bg-gray-200 font-semibold'; ?>">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4a2 2 0 001-1.73z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.27 6.96L12 12.01l8.73-5.05" />
            </svg>
            Tambah Produk
          </a>
        </li>
        <li>
          <a href="/inventobox/pages/admin/produk_list.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition<?php if ($currentPage === 'produk_list.php') echo ' bg-gray-200 font-semibold'; ?>">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect x="3" y="7" width="18" height="13" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M16 3v4M8 3v4M3 11h18" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Manajemen Produk
          </a>
        </li>
        <li>
          <a href="/pages/admin/laporan.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition<?php if ($currentPage === 'laporan.php') echo ' bg-gray-200 font-semibold'; ?>">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2h6v2a2 2 0 002 2h1a2 2 0 002-2V7a2 2 0 00-2-2h-1a2 2 0 00-2 2v2H9V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h1a2 2 0 002-2z" />
            </svg>
            Laporan
          </a>
        </li>

      <?php elseif ($role === 'gudang'): ?>
        <li>
          <a href="/pages/gudang/barang_masuk.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4v16m8-8H4" />
            </svg>
            Barang Masuk
          </a>
        </li>
        <li>
          <a href="/pages/gudang/barang_keluar.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16h16M4 12h8m-8-4h16" />
            </svg>
            Barang Keluar
          </a>
        </li>
        <li>
          <a href="/pages/gudang/stok_gudang.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M20 13V5a2 2 0 00-2-2H6a2 2 0 00-2 2v8m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
            </svg>
            Stok Gudang
          </a>
        </li>

      <?php elseif ($role === 'toko'): ?>
        <?php if ($tipe === 'online'): ?>
          <li>
            <a href="/pages/toko/dashboard_online.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3h18v4H3zM5 7v10a2 2 0 002 2h10a2 2 0 002-2V7" />
              </svg>
              Dashboard Online
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="/pages/toko/dashboard_offline.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3h18v6H3zM5 9v10a2 2 0 002 2h10a2 2 0 002-2V9" />
              </svg>
              Dashboard Offline
            </a>
          </li>
        <?php endif; ?>
        <li>
          <a href="/pages/toko/barang_terima.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 4v16h16V4H4zm4 8h8m-8 4h8" />
            </svg>
            Barang Terima
          </a>
        </li>
        <li>
          <a href="/pages/toko/stok_toko.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            Stok Toko
          </a>
        </li>
        <li>
          <a href="/pages/toko/laporan.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2h6v2a2 2 0 002 2h1a2 2 0 002-2V7a2 2 0 00-2-2h-1a2 2 0 00-2 2v2H9V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h1a2 2 0 002-2z" />
            </svg>
            Laporan
          </a>
        </li>
      <?php endif; ?>

    </ul>
  </nav>
</div>