<?php
session_start();

if (!isset($_POST['verification_code'])) {
    echo "Lỗi xác thực!";
    exit;
}

$enteredCode = $_POST['verification_code'];
$storedCode = $_SESSION['verification_code'] ?? null;
$email = $_SESSION['email_temp'] ?? null;
echo $enteredCode.'<br>';
echo $storedCode;
if ($enteredCode == $storedCode) {
    echo "Xác thực thành công! Tiến hành đăng ký tài khoản...";
    
    

} else {
    echo "Mã xác thực không đúng!";
}
?>
