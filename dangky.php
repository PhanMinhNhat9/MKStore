<?php
require 'PHPMailer-master\src\Exception.php';
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
function sendVerificationEmail($email, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
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

session_start();
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $verificationCode = rand(100000, 999999); // Mã OTP ngẫu nhiên 6 chữ số
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['email_temp'] = $email;
    if (sendVerificationEmail($email, $verificationCode)) {
        echo "Mã xác thực đã gửi! <a href='verify.php'>Nhập mã tại đây</a>";
    } else {
        echo "Gửi email thất bại!";
    }
}


?>