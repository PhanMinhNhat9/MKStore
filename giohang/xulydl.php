<?php
require_once '../config.php';
$pdo = connectDatabase();
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';

// Thêm vào bảng giohang
// $sql = "UPDATE giohang SET idgh = :phone WHERE idgh ='0'";
// $stmt = $pdo->prepare($sql);
// $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
// $stmt->execute();

try {
    //Bắt đầu transaction
    $pdo->beginTransaction();
    // Tính tổng tiền đơn hàng
    $stmt = $pdo->prepare("SELECT SUM(thanhtien) AS thanhtien FROM giohang WHERE idkh = :idkh");
    $stmt->execute(['idkh' => $_SESSION['user']['iduser']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tongtien = $result['thanhtien'];
    
    // Thêm vào đơn hàng
    $stmt = $pdo->prepare("INSERT INTO donhang (sdt, tenkh, tongtien) 
                        VALUES (:sdt, :tenkh, :tongtien)");
    $stmt->execute([
        ':sdt' => $phone, 
        ':tenkh' => $name,
        ':tongtien' => $tongtien, 
    ]);

    //Lấy ID đơn hàng mới
    $iddh = $pdo->lastInsertId();

    //Thêm chi tiết cho đơn hàng
    $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia, giagoc, giagiam) 
                            SELECT :iddh, idsp, soluong, thanhtien, giagoc, giagiam FROM giohang WHERE idkh = :idkh");
    $stmt->execute([
        'iddh' => $iddh,
        'idkh' => $_SESSION['user']['iduser']
    ]);

    // Xóa sản phẩm trong `giohang`
    $stmt = $pdo->prepare("DELETE FROM giohang WHERE idkh = ?");
    $stmt->execute([$_SESSION['user']['iduser']]);

    //Hoàn thành transaction
    $pdo->commit();
    header("Location: ttthanhtoan.php?sdt=$phone&hoten=$name"); 
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: hienthigiohang.php"); 
    exit;
}

?>
