<?php
require_once 'config.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="FMquenMK.css">
</head>
<body>
    <div class="container">
        <h2><i class="fa fa-key"></i> Quên Mật Khẩu</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if ($step == 1): ?>
            <form method="POST">
                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-phone"></i>
                    <input type="text" name="sdt" placeholder="Số điện thoại" required>
                </div>
                <button type="submit" name="verify"><i class="fa fa-check"></i> Xác nhận</button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="POST">
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                </div>
                <button type="submit" name="reset_password"><i class="fa fa-save"></i> Đặt lại mật khẩu</button>
            </form>
        <?php endif; ?>
        <a href="GUI&dangnhap.php" class="forgot-password">Quay lại đăng nhập</a>
    </div>
</body>
</html>

