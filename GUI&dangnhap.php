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
    $tendn = trim($_POST['tendn'] ?? '');
    $matkhau = trim($_POST['matkhau'] ?? '');
    $error = dangnhap($tendn, $matkhau); 
}

// Kiểm tra nếu đã hết thời gian khóa, reset lỗi
if ($_SESSION['login_attempts'] >= 2 && time() >= $_SESSION['lock_time']) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lock_time'] = 0;
    $error = ""; // Reset lỗi
}
$lockTime = $_SESSION['lock_time']-time();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="FMdangnhap.css">
    <script>
        let lockTime = <?= $lockTime; ?>;
        if (lockTime > 0) {
            setTimeout(() => {
                alert("Bạn có thể đăng nhập lại ngay bây giờ.");
                document.querySelector(".error").textContent = "";
            }, lockTime * 1000);
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <p class='error'><?= !empty($error) ? $error : ""; ?></p>
        <form method="POST">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="tendn" placeholder="Tên đăng nhập" required>
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="matkhau" placeholder="Mật khẩu" required>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            </div>
            <button type="submit"><i class="fa fa-sign-in-alt"></i> Đăng nhập</button>
            <a href="GUI&quenMK.php" class="forgot-password">Quên mật khẩu?</a>
        </form>
    </div>
</body>
</html>
