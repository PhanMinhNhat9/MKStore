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
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="FMdangnhap.css">
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <p class='error'><?= !empty($_SESSION['login_error']) ? $_SESSION['login_error'] : ""; unset($_SESSION['login_error']);?></p>
        <form method="POST">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="tendn" placeholder="Tên đăng nhập" required>
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="matkhau" id="password" placeholder="Mật khẩu" required>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            </div>
            <div class="checkbox-container">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">Hiển thị mật khẩu</label>
            </div>
            <button type="submit"><i class="fa fa-sign-in-alt"></i> Đăng nhập</button>
            
            <div class="auth-links">
                <a href="GUI&quenMK.php" class="forgot-password">Quên mật khẩu?</a>
                <a href="dangky/giaodiendangky.php" class="dangkynd">Đăng ký</a>
            </div>
        </form>
    </div>
    <script>
    let lockTime = <?= $lockTime; ?>;
if (lockTime > 0) {
    let countdownElement = document.createElement("p");
    countdownElement.className = "countdown";
    countdownElement.style.marginTop = "15px"; // Tạo khoảng cách với phần tử trên
    document.querySelector(".login-container").appendChild(countdownElement);

    function updateCountdown() {
        let minutes = Math.floor(lockTime / 60);
        let seconds = lockTime % 60;
        countdownElement.innerHTML = `<strong style="color: red; font-size: 14px;">⏳ Bạn có thể đăng nhập lại sau: ${minutes} phút ${seconds} giây</strong>`;
        
        if (lockTime > 0) {
            lockTime--;
            setTimeout(updateCountdown, 1000);
        } else {
            countdownElement.innerHTML = `<strong style="color: green; font-size: 14px;">✅ Bạn có thể đăng nhập lại ngay bây giờ.</strong>`;
            document.querySelector(".error").textContent = "";
        }
    }
    
    updateCountdown();
}

</script>

    <script>
        document.getElementById("showPassword").addEventListener("change", function () {
    let passwordInput = document.getElementById("password");
    if (this.checked) {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
});
    </script>
</body>
</html>
