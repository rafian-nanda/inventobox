<?php
$username = ucfirst($_SESSION['username']);
$role = ucfirst($_SESSION['role']);
$tipe = $_SESSION['tipe_toko'] ?? '';
$roleDisplay = $role === 'Toko' ? "$role ($tipe)" : $role;
?>

<header class="w-full bg-white border-b border-gray-200 px-4 py-3 shadow-sm relative">
  <div class="flex items-center justify-between">
    <!-- Left: Hamburger (mobile only) -->
    <div class="sm:hidden">
      <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    <!-- Center: Logo (only mobile) -->
    <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 sm:hidden text-base font-semibold text-gray-700">
      InventoBox
    </div>

    <!-- Right: Avatar Dropdown -->
    <div x-data="{ open: false }" class="ml-auto relative">
      <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
        <img src="https://i.pravatar.cc/40?u=<?= $username ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>

      <!-- Dropdown -->
      <div x-show="open" x-cloak @click.away="open = false"
           class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-md shadow-lg z-50">
        <div class="px-4 py-2 text-sm text-gray-700">
          <strong><?= $username ?></strong><br>
          <span class="text-xs text-gray-500"><?= $roleDisplay ?></span>
        </div>
        <div class="border-t border-gray-100"></div>
        <a href="/pages/profile.php" class="block px-4 py-2 text-sm hover:bg-gray-100 text-gray-700">Edit Profile</a>
        <a href="/inventobox/auth/logout.php" class="block px-4 py-2 text-sm hover:bg-gray-100 text-red-600">Logout</a>
      </div>
    </div>
  </div>
</header>
