<?php
    require 'PHPMailer-master\src\Exception.php';
    require 'PHPMailer-master\src\PHPMailer.php';
    require 'PHPMailer-master\src\SMTP.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    function sendVerificationEmail($email, $verificationCode) {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8'; // Đảm bảo email sử dụng UTF-8
            $mail->Host = 'smtp.gmail.com'; // SMTP của Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'nguyentuanand2589@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'ykrq borr osxw urtl'; // Thay bằng mật khẩu ứng dụng của Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            // Cấu hình người gửi và người nhận
            $mail->setFrom('nguyentuanand2589@gmail.com', 'Hệ thống xác thực');
            $mail->addAddress($email);
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Xác thực tài khoản của bạn';
            $mail->Body    = "
                                <h3>Chào mừng bạn đến với hệ thống của chúng tôi!</h3>
                                <p>Vui lòng nhập mã xác thực sau để hoàn tất đăng ký:</p>
                                <h2>$verificationCode</h2>
                                <p>Nếu bạn không yêu cầu đăng ký, vui lòng bỏ qua email này.</p>
                            ";
            // Gửi email
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['email'])) {
            session_start();
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $tendn = $_POST['tendn'];
            $mk = $_POST['mk'];
            $sdt = $_POST['sdt'];
            $diachi = $_POST['diachi'];
            $anh = $_FILES['anh'];
            $verificationCode = rand(100000, 999999); // Mã OTP ngẫu nhiên 6 chữ số
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['email_temp'] = $email;
            $_SESSION['hoten_temp'] = $hoten;
            $_SESSION['tendn_temp'] = $tendn;
            $_SESSION['mk_temp'] = $mk;
            $_SESSION['sdt_temp'] = $sdt;
            $_SESSION['diachi_temp'] = $diachi;
            $_SESSION['anh_temp'] = $anh;
            if (sendVerificationEmail($email, $verificationCode)) {
                echo "<script> alert('Mã xác thực đã được gửi!'); 
                    window.location.href = 'xacthuc.php' </script>";
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
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
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
                    <input type="email" name="email" placeholder="Email" required>
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
            <div class="input-group">
                <input type="file" name="anh" accept="image/*" onchange="previewAvatar(event)">
            </div>
            <input type="submit" name="dangkytkuser" value="Đăng ký">
        </form>
    </div>
    
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
