<?php
    session_start();
    if (!isset($_SESSION['email_temp'])) {
        echo "Không có yêu cầu xác thực nào!";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="xacthuc.css">
</head>
<body>
    <div class="verify-container">
        <h2>Xác thực tài khoản</h2>
        <form action="thuchiendk.php" method="POST">
            <div class="input-group">
                <i class="fa fa-key"></i>
                <input type="text" name="verification_code" placeholder="Nhập mã xác thực" required>
            </div>
            <button type="submit">Xác nhận</button>
        </form>
    </div>
</body>
</html>
