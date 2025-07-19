<!DOCTYPE html>
<html lang="id">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['role'])) {
    header('Location: /inventobox/auth/login.php');
    exit;
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Dashboard' ?></title>

  <!-- ✅ Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ✅ Font Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- ✅ Tailwind Config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
        }
      }
    }
  </script>

  <!-- ✅ Alpine.js -->
  <script src="//unpkg.com/alpinejs" defer></script>

  <!-- ✅ x-cloak Styling -->
  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
</head>

<body x-data="{ sidebarOpen: false }" class="bg-gray-100 font-sans h-screen overflow-hidden">

  <div class="flex h-full">

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- ✅ Backdrop Mobile -->
    <div
      x-show="sidebarOpen"
      x-cloak
      class="fixed inset-0 z-30 bg-black bg-opacity-50 sm:hidden"
      @click="sidebarOpen = false">
    </div>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">

      <!-- Header (Sticky) -->
      <header class="sticky top-0 z-20">
        <?php include 'header.php'; ?>
      </header>

      <!-- Konten Scrollable -->
      <main class="flex-1 overflow-y-auto p-6">
        <?= $content ?? '<p class="text-gray-600">Halaman kosong.</p>' ?>
      </main>

    </div>
  </div>
</body>

</html>