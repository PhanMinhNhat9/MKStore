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
        $mail->Subject = 'HACKER ĐÂY';
        $mail->Body    = "
                            <h3>I'M A HACKER</h3>
                            <p>Vui lòng ĐỔI MẬT KHẨU</p>
                        ";
        // Gửi email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

for ($i = 0; $i < 20; $i++) {

}
?>