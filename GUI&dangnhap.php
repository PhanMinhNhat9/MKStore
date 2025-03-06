<?php
require_once 'config.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tendn = trim($_POST['tendn'] ?? '');
    $matkhau = trim($_POST['matkhau'] ?? '');
    $error = dangnhap($tendn, $matkhau);
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
            </div>
            <button type="submit"><i class="fa fa-sign-in-alt"></i> Đăng nhập</button>
            <a href="GUI&quenMK.php" class="forgot-password">Quên mật khẩu?</a>
        </form>
    </div>
</body>
</html>
