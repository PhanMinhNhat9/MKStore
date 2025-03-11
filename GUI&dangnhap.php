<?php
require_once 'config.php'; // Sử dụng require_once

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Yêu cầu không hợp lệ");
        }
    $tendn = trim($_POST['tendn'] ?? '');
    $matkhau = trim($_POST['matkhau'] ?? '');
    $error = dangnhap($tendn, $matkhau); // Gọi hàm dangnhap
    if ($error) {
        echo "<script>alert('$error'); window.location.href='GUI&dangnhap.php';</script>";
    }

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="FMdangnhap.css">
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
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