<?php
    session_start();
    if (!isset($_SESSION['email_temp'])) {
        exit;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xác thực tài khoản</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../fontawesome/css/all.min.css" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { poppins: ['Poppins', 'sans-serif'] }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(45deg, #6b7280, #3b82f6, #8b5cf6, #ec4899);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
    }
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-500 via-blue-400 to-blue-800">
  <div class="bg-white/20 backdrop-blur-xl p-8 rounded-2xl w-96 shadow-2xl text-white space-y-6">
    <!-- Tiêu đề -->
    <div class="flex justify-center border-b border-white/30 pb-3">
      <span class="text-2xl font-semibold tracking-wide">
        <i class="fa fa-check-circle mr-2"></i> Xác thực tài khoản
      </span>
    </div>

    <!-- Form -->
    <form action="../auth/thuchiendk.php" method="POST" enctype="multipart/form-data" class="space-y-5">
      <!-- Mã xác thực -->
      <div>
        <label for="verification_code" class="text-sm text-gray-200 block">Mã xác thực</label>
        <div class="flex items-center bg-white/20 rounded px-3 mt-1">
          <i class="fa fa-key text-white mr-2"></i>
          <input
            id="verification_code"
            type="text"
            name="verification_code"
            required
            class="w-full p-2 bg-transparent text-white focus:outline-none"
            autocomplete="off"
          />
        </div>
      </div>

      <!-- Nút xác nhận -->
      <button
        type="submit"
        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white py-2 rounded-lg font-semibold shadow-md transition"
      >
        Xác nhận
      </button>
    </form>
  </div>
</body>
</html>


