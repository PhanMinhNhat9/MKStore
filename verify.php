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
</head>
<body>
    <h2>Xác thực tài khoản</h2>
    <form action="verify_process.php" method="POST">
        <label>Nhập mã xác thực:</label>
        <input type="text" name="verification_code" required>
        <button type="submit">Xác nhận</button>
    </form>
</body>
</html>
