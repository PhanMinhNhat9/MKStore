<?php
require_once 'config.php';
$pdo = connectDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idsp = intval($_POST["idsp"]);
    $tensp = htmlspecialchars($_POST["tensp"]);
    $mota = htmlspecialchars($_POST["mota"]);
    $giaban = floatval($_POST["giaban"]);
    $soluong = intval($_POST["soluong"]);
    $iddm = intval($_POST["iddm"]);
    
    // Xử lý hình ảnh nếu có upload
    if (!empty($_FILES["anh"]["name"])) {
        $target_dir = "picture\\";
        $target_file = $target_dir . basename($_FILES["anh"]["name"]);
        
        if (move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
            $anh = addslashes($target_file);
            $sql = "UPDATE sanpham SET tensp = :tensp, mota = :mota, giaban = :giaban, soluong = :soluong, anh = :anh, iddm = :iddm WHERE idsp = :idsp";
        } else {
            echo "Lỗi khi tải ảnh lên.";
            exit();
        }
    } else {
        $sql = "UPDATE sanpham SET tensp = :tensp, mota = :mota, giaban = :giaban, soluong = :soluong, iddm = :iddm WHERE idsp = :idsp";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tensp', $tensp, PDO::PARAM_STR);
    $stmt->bindParam(':mota', $mota, PDO::PARAM_STR);
    $stmt->bindParam(':giaban', $giaban, PDO::PARAM_STR);
    $stmt->bindParam(':soluong', $soluong, PDO::PARAM_INT);
    $stmt->bindParam(':iddm', $iddm, PDO::PARAM_INT);
    $stmt->bindParam(':idsp', $idsp, PDO::PARAM_INT);
    if (!empty($_FILES["anh"]["name"])) {
        $stmt->bindParam(':anh', $anh, PDO::PARAM_STR);
    }
    
    if ($stmt->execute()) {
        echo "<script> alert('Cập nhật thành công!');
        window.location.replace('trangchuadmin.html?id=2');</script>";
    } else {
        echo "Lỗi cập nhật.";
    }
}
