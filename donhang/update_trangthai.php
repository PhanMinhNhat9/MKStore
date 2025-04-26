<?php
    require_once '../config.php';
    $pdo = connectDatabase();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iddh']) && isset($_POST['trangthai'])) {
        $iddh = $_POST['iddh'];
        $trangthai = $_POST['trangthai'];

        // Cập nhật trạng thái đơn hàng
        $sql = "UPDATE donhang SET trangthai = :trangthai WHERE iddh = :iddh";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':trangthai', $trangthai, PDO::PARAM_STR);
        $stmt->bindParam(':iddh', $iddh, PDO::PARAM_INT);
        $stmt->execute(); // THỰC THI update đơn hàng

        // Cập nhật trạng thái yêu cầu đơn hàng
        if (isset($_POST['idyc'])) {
            $idyc = $_POST['idyc'];
            $sql2 = "UPDATE yeucaudonhang SET trangthai = 1 WHERE idyc = :idyc";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindParam(':idyc', $idyc, PDO::PARAM_INT);
            $stmt2->execute(); // THỰC THI update yêu cầu đơn hàng
        }

        // Sau khi thành công, chuyển trang
        echo "<script> 
                window.location.href = '../donhang/hienthidonhang.php';
            </script>";
    }
?>
