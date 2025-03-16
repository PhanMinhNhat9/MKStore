<?php
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iddh']) && isset($_POST['trangthai'])) {
    $iddh = $_POST['iddh'];
    $trangthai = $_POST['trangthai'];
    $sql = "UPDATE donhang SET trangthai = :trangthai WHERE iddh = :iddh";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':trangthai', $trangthai, PDO::PARAM_STR);
    $stmt->bindParam(':iddh', $iddh, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "<script src='../trangchuadmin.js'></script>";
        echo "<script> 
                alert('Xác nhận đơn hàng thành công!');
                goBack();
            </script>";
        exit();
    } else {
        echo "<script>alert('Cập nhật trạng thái thất bại!');</script>";
    }
}
?>
