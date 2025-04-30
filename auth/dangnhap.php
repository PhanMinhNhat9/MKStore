<?php
  require_once '../config.php';

  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
  }
  if (!isset($_SESSION['lock_time']) || !is_numeric($_SESSION['lock_time'])) {
    $_SESSION['lock_time'] = 0;
  }

  $error = "";

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
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    }
  } else {
    $_SESSION['login_error'] = "";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
}

  if ($_SESSION['login_attempts'] >= 2 && time() > $_SESSION['lock_time']) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lock_time'] = 0;
    $error = "";
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
<link rel="stylesheet" href="../fontawesome/css/all.min.css">
<link rel="icon" href="../picture/logoTD.png" type="image/png">
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
  }
  #showPassword:checked::after {
    content: '✔';
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 0.75rem;
    width: 100%;
    height: 100%;
  }
  body {
    background: linear-gradient(45deg, #6b7280, #3b82f6, #8b5cf6, #ec4899);
    background-size: 400% 400%;
    animation: gradient 15s ease infinite;
    min-h-screen flex items-center justify-center;
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
      <span class="text-2xl font-semibold tracking-wide">Đăng nhập</span>
    </div>
    <!-- Hiển thị lỗi -->
    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="bg-red-500/80 text-white text-sm text-center px-4 py-2 rounded shadow">
        <?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
      </div>
    <?php endif; ?>
    <!-- Hiển thị thời gian chờ -->
    <?php if ($lockTime > 0): ?>
      <div id="countdown" class="text-yellow-200 text-sm text-center font-medium"></div>
    <?php endif; ?>
    <!-- Form -->
    <form method="POST" class="space-y-5">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
      <!-- Username -->
      <div>
        <label class="text-sm text-gray-200">Tên đăng nhập</label>
        <div class="flex items-center bg-white/20 rounded px-3 mt-1">
          <i class="fa fa-user text-white mr-2"></i>
          <input type="text" name="tendn"
          class="w-full p-2 bg-transparent text-white focus:outline-none"
          required autocomplete="off">
        </div>
      </div>
      <!-- Password -->
      <div>
        <label class="text-sm text-gray-200">Mật khẩu</label>
        <div class="flex items-center bg-white/20 rounded px-3 mt-1">
          <i class="fa fa-lock text-white mr-2"></i>
          <input type="password" name="matkhau" id="password"
          class="w-full p-2 bg-transparent text-white focus:outline-none"
          required>
        </div>
      </div>
      <!-- Checkbox hiển thị mật khẩu -->
      <div class="flex items-center gap-2 text-sm text-gray-200">
        <input type="checkbox" id="showPassword" 
        class="appearance-none h-4 w-4 rounded bg-white/20 checked:bg-blue-500 
        checked:text-white flex items-center justify-center transition duration-150 ease-in-out" />
        <label for="showPassword">Hiển thị mật khẩu</label>
      </div>
      <!-- Submit -->
      <button type="submit"
        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 
        hover:to-blue-500 text-white py-2 rounded-lg font-semibold shadow-md transition">
        Đăng nhập
      </button>
      <!-- Liên kết -->
      <div class="text-sm text-center text-white/100">
        <a href="../auth/quenmk.php" class="hover:underline">Quên mật khẩu?</a> |
        <a href="../auth/dangky.php" class="hover:underline">Đăng ký</a>
      </div>
    </form>
  </div>
<!-- Script -->
<script>
  document.getElementById("showPassword").addEventListener("change", function () {
    let passwordInput = document.getElementById("password");
    passwordInput.type = this.checked ? "text" : "password";
  });
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