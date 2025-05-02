<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../script.js"></script>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<?php
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy giá trị từ form
    $iddh = $_POST['iddh'] ?? '';
    $tenkh = trim($_POST['tenkh'] ?? '');

    // Kiểm tra nếu cả hai đều không rỗng
    if ($iddh && $tenkh) {
        // Cập nhật tên khách hàng trong đơn hàng
        $sql = "UPDATE donhang SET tenkh = :tenkh WHERE iddh = :iddh";
        $stmt = $pdo->prepare($sql);

        // Thực thi truy vấn với các tham số đã bind
        if ($stmt->execute(['tenkh' => $tenkh, 'iddh' => $iddh])) {
            // Thực hiện quay lại trang trước khi cập nhật
            echo "<script> 
                    goBack();
                </script>";
        } 
}
}
?>
