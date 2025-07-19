<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: /index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - InventoBox</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom Theme: Inter + Monokrom -->
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
  <div class="w-full max-w-md px-6">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
      <h1 class="text-3xl font-bold text-center text-black mb-1">InventoBox</h1>
      <p class="text-center text-gray-500 mb-6">Silakan masuk ke akun Anda</p>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm border border-red-300">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <form action="proses_login.php" method="POST" class="space-y-5">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" name="username" id="username" required
                 class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black text-black bg-white">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <div class="relative mt-1">
            <input type="password" name="password" id="password" required
                   class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black text-black bg-white">
            <button type="button" onclick="togglePassword()"
              class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-black">
              <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit"
                class="w-full bg-black hover:bg-gray-900 text-white py-2 rounded-lg font-semibold transition duration-200">
          Masuk
        </button>
      </form>
    </div>
  </div>

  <script>
    let showing = false;

    function togglePassword() {
      const input = document.getElementById("password");
      const icon = document.getElementById("eye-icon");

      if (showing) {
        input.type = "password";
        icon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
        showing = false;
      } else {
        input.type = "text";
        icon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7
                a9.969 9.969 0 012.362-4.328m3.142-2.462A9.953 9.953 0 0112 5c4.478 0
                8.268 2.943 9.542 7a9.974 9.974 0 01-4.307 5.033M15 12a3 3 0 11-6 0 3 3
                0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3l18 18"/>
        `;
        showing = true;
      }
    }
  </script>
</body>
</html>
