<?php
    include "../config.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $tendn = $_POST['tendn'];
            $mk = $_POST['mk'];
            $sdt = $_POST['sdt'];
            $diachi = $_POST['diachi'];
           
            $verificationCode = rand(100000, 999999);
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['email_temp'] = $email;
            $_SESSION['hoten_temp'] = $hoten;
            $_SESSION['tendn_temp'] = $tendn;
            $_SESSION['mk_temp'] = $mk;
            $_SESSION['sdt_temp'] = $sdt;
            $_SESSION['diachi_temp'] = $diachi;
            
            if (sendVerificationEmail($email, $verificationCode)) {
                echo "<script> alert('Mã xác thực đã được gửi!'); 
                               window.location.href = 'xacthuc.php';
                     </script>";

            } else {
                echo "Gửi email thất bại!";
            }
        } else {
            echo "<script> alert('Vui lòng nhập email!'); </script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="giaodiendangky.css">
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký</h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="hoten" placeholder="Họ và Tên">
                </div>
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="tendn" placeholder="Tên đăng nhập">
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" >
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="mk" placeholder="Mật khẩu">
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <i class="fa fa-phone"></i>
                    <input type="text" name="sdt" placeholder="Số điện thoại">
                </div>
                <div class="input-group">
                    <i class="fa fa-map-marker-alt"></i>
                    <input type="text" name="diachi" placeholder="Địa chỉ">
                </div>
            </div>
            <div class="avatar-preview">
                <img id="preview" src="#" alt="Ảnh đại diện">
            </div>
            <input type="submit" name="dangkytkuser" value="Đăng ký">
        </form>
    </div>
</body>
</html>
