<?php
require_once 'config.php'; // Sử dụng require_once

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Khởi tạo biến tránh lỗi
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lock_time']) || !is_numeric($_SESSION['lock_time'])) {
    $_SESSION['lock_time'] = 0; // Đặt giá trị mặc định nếu bị lỗi
}

$error = ""; // Mặc định không có lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Yêu cầu không hợp lệ");
    }
    $tendn = isset($_POST['tendn']) ? trim($_POST['tendn']) : '';
    $matkhau = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';
    if (!empty($tendn) && !empty($matkhau)) {

        $error = dangnhap($tendn, $matkhau);
        if (!empty($error)) {
            $_SESSION['login_error'] = $error;
        header("Location: " . $_SERVER['PHP_SELF']); // Chuyển hướng để xóa dữ liệu POST
            exit();
        }
        // Xóa dữ liệu nhập để tránh bị lưu lại khi reload
    
    } else {
        $_SESSION['login_error'] = "";  
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    }


}

// // Kiểm tra nếu đã hết thời gian khóa, reset lỗi
if ($_SESSION['login_attempts'] >= 2 && time() > $_SESSION['lock_time']) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lock_time'] = 0;
    $error = ""; // Reset lỗi
    $tendn = "";
    $matkhau = "";
}
$lockTime = $_SESSION['lock_time'] - time();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    body { font-family: 'Poppins', sans-serif; }
    /* Dấu tick tuỳ chỉnh khi checkbox được chọn */
    #showPassword:checked::after {
        content: '✔';
        display: block;
        color: white;
        font-size: 0.75rem;
        line-height: 1rem;
        text-align: center;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-800 via-blue-400 via-40% via-blue-300 via-50% to-blue-800">
  <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl w-96 shadow-lg text-white">
    <div class="flex justify-between mb-6 border-b border-gray-400 pb-2">
      <button class="text-white font-semibold border-b-2 border-white-700">Sign In</button>
    </div>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="text-red-200 text-sm text-center mb-4 font-medium"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
    <?php endif; ?>

    <?php if ($lockTime > 0): ?>
      <div id="countdown" class="text-yellow-300 text-sm text-center mb-4"></div>
    <?php endif; ?>

    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
      <div class="mb-4">
  <label class="text-sm text-gray-300">Username</label>
  <div class="flex items-center bg-white/20 rounded px-2 mt-1">
    <i class="fa fa-user text-white mr-2"></i>
    <input type="text" name="tendn"
           class="w-full p-2 bg-transparent text-white focus:outline-none focus:ring-2 focus:ring-[#007bff]"
           required>
  </div>
</div>

<div class="mb-4">
  <label class="text-sm text-gray-300">Password</label>
  <div class="flex items-center bg-white/20 rounded px-2 mt-1">
    <i class="fa fa-lock text-white mr-2"></i>
    <input type="password" name="matkhau" id="password"
           class="w-full p-2 bg-transparent text-white focus:outline-none focus:ring-2 focus:ring-[#007bff]"
           required>
  </div>
</div>


<div class="flex items-center mb-4 text-sm text-gray-300">
  <input type="checkbox" id="showPassword"
    class="mr-2 h-5 w-5 appearance-none bg-white/20 rounded-sm checked:bg-blue-500
           relative before:content-[''] before:block before:absolute before:inset-0 before:rounded-sm transition" />
  <label for="showPassword">Hiển thị mật khẩu</label>
</div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-300 transition">SIGN IN</button>

      <div class="text-sm text-center mt-4">
        <a href="GUI&quenMK.php" class="text-white-200">Quên mật khẩu?</a> |
        <a href="dangky/giaodiendangky.php" class="text-white-200">Đăng ký</a>
      </div>
    </form>
  </div>

  <script>
    // Toggle hiển thị mật khẩu
    document.getElementById("showPassword").addEventListener("change", function () {
      let passwordInput = document.getElementById("password");
      passwordInput.type = this.checked ? "text" : "password";
    });

    // Đếm ngược thời gian khóa nếu cần
    <?php if ($lockTime > 0): ?>
      let lockTime = <?= $lockTime ?>;
      const countdown = document.getElementById("countdown");

      function updateCountdown() {
        let m = Math.floor(lockTime / 60);
        let s = lockTime % 60;
        countdown.textContent = `⏳ Vui lòng thử lại sau: ${m} phút ${s} giây`;
        if (lockTime > 0) {
          lockTime--;
          setTimeout(updateCountdown, 1000);
        } else {
          countdown.textContent = "✅ Bạn có thể đăng nhập lại.";
        }
      }
      updateCountdown();
    <?php endif; ?>
  </script>
</body>
</html>


