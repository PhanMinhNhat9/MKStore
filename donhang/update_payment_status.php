<?php
require_once '../config.php';

// Kiểm tra CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Lỗi xác thực CSRF.");
}

// Kiểm tra quyền truy cập: chỉ cho phép khách hàng (quyen == 1)
if ($_SESSION['user']['quyen'] != 1) {
    die("Bạn không có quyền thực hiện thao tác này.");
}

// Kiểm tra dữ liệu đầu vào
if (!isset($_POST['iddh'])) {
    die("Thiếu mã đơn hàng.");
}

$iddh = trim($_POST['iddh']);

try {
    $pdo = connectDatabase();
    $sql = "UPDATE donhang SET trangthai = 'Đã thanh toán' WHERE iddh = :iddh AND trangthai NOT IN ('Đã thanh toán', 'Hủy đơn') AND sdt = :sdt";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['iddh' => $iddh, 'sdt' => $_SESSION['user']['sdt']]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../trangchu.php?status=nhanhangT");
    } else {
        header("Location: ../trangchu.php?status=nhanhangF");
    }
} catch (PDOException $e) {
    die("Lỗi cập nhật trạng thái: " . $e->getMessage());
}
?>