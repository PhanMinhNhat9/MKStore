<?php
require_once '../config.php';
$error = "";
$success = "";
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["verify"])) {
        $email = trim($_POST["email"] ?? '');
        $sdt = trim($_POST["sdt"] ?? '');
        $user = verifyUser($email, $sdt);
        if ($user) {
            $_SESSION["reset_user_id"] = $user["iduser"];
            $step = 2;
        } else {
            $error = "Thông tin không chính xác. Vui lòng thử lại!";
        }
    } elseif (isset($_POST["reset_password"])) {
        $newPassword = trim($_POST["new_password"] ?? '');
        $confirmPassword = trim($_POST["confirm_password"] ?? '');

        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $iduser = $_SESSION["reset_user_id"] ?? null;
            if ($iduser && updatePassword($iduser, $newPassword)) {
                unset($_SESSION["reset_user_id"]);
                $success = "Mật khẩu đã được cập nhật thành công! Hãy đăng nhập lại.";
                $step = 1;
            } else {
                $error = "Có lỗi xảy ra. Vui lòng thử lại!";
            }
        } else {
            $error = "Mật khẩu không khớp hoặc quá ngắn!";
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quên Mật Khẩu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />
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
    #showPassword:checked::after {
        content: '✔';
        color: white;
        font-size: 0.75rem;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-500 via-blue-400 to-blue-800">
    <div class="bg-white/20 backdrop-blur-xl p-8 rounded-2xl w-full max-w-md text-white space-y-6 shadow-xl">
        <h2 class="text-2xl font-semibold text-center mb-4 border-b border-white/30 pb-3">Quên Mật Khẩu</h2>
        <?php if (!empty($error)) echo "<p class='bg-red-500/80 text-white px-4 py-2 rounded text-sm'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='bg-green-500/80 text-white px-4 py-2 rounded text-sm'>$success</p>"; ?>
        <?php if ($step == 1): ?>
            <form method="POST" class="space-y-4">
                <div class="space-y-1">
                    <label for="email" class="text-sm text-gray-200 block mb-1">Email</label>
                    <div class="flex items-center bg-white/10 rounded-md px-3">
                    <i class="fas fa-envelope mr-2"></i>
                    <input id="email" type="email" name="email" required autocomplete="off" class="w-full bg-transparent p-2 focus:outline-none text-white placeholder-white/50"/>
                    </div>
                </div>
                <div class="space-y-1">
                    <label for="sdt" class="text-sm text-gray-200 block mb-1">Số điện thoại</label>
                    <div class="flex items-center bg-white/10 rounded-md px-3">
                    <i class="fas fa-phone mr-2"></i>
                    <input id="sdt" type="text" name="sdt" required autocomplete="off" class="w-full bg-transparent p-2 focus:outline-none text-white"/>
                    </div>
                </div>

                <button type="submit" name="verify" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 
                    hover:to-blue-500 text-white py-2 rounded-lg font-semibold shadow-md transition"> Xác nhận 
                </button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="POST" class="space-y-4">
                <div class="space-y-1">
                    <label for="new_password" class="text-sm text-gray-200 block mb-1">Mật khẩu mới</label>
                    <div class="flex items-center bg-white/10 rounded-md px-3">
                    <i class="fas fa-lock mr-2"></i>
                    <input id="new_password" type="password" name="new_password" required class="w-full bg-transparent p-2 focus:outline-none text-white"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="confirm_password" class="text-sm text-gray-200 block mb-1">Xác nhận mật khẩu</label>
                    <div class="flex items-center bg-white/10 rounded-md px-3">
                    <i class="fas fa-lock mr-2"></i>
                    <input id="confirm_password" type="password" name="confirm_password" required class="w-full bg-transparent p-2 focus:outline-none text-white"/>
                    </div>
                </div>

                <!-- Checkbox hiện mật khẩu -->
                <div class="flex items-center space-x-2 text-sm text-gray-300">
                    <input type="checkbox" id="showPassword" class="appearance-none h-4 w-4 rounded bg-white/20 checked:bg-blue-500 
                    checked:text-white flex items-center justify-center transition duration-150 ease-in-out" onclick="togglePasswords()" />
                    <label for="show_password" class="cursor-pointer select-none">Hiển thị mật khẩu</label>
                </div>

                <button type="submit" name="reset_password" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 
                        hover:to-blue-500 text-white py-2 rounded-lg font-semibold shadow-md transition">
                    Đặt lại mật khẩu
                </button>
            </form>
        <?php endif; ?>
        <div class="text-sm text-center">
            <a href="../auth/dangnhap.php" class="text-sm text-center text-white/100 hover:underline">Quay lại đăng nhập</a>
        </div>
    </div>
<!-- Script toggle hiển thị mật khẩu -->
<script>
    function togglePasswords() {
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const isChecked = document.getElementById('show_password').checked;
        newPassword.type = isChecked ? 'text' : 'password';
        confirmPassword.type = isChecked ? 'text' : 'password';
    }
</script>
</body>
</html>
