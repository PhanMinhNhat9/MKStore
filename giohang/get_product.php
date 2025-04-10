<?php
header('Content-Type: application/json'); // Đảm bảo header là JSON
require_once '../config.php';
$pdo = connectDatabase(); // Kết nối với CSDL
if (isset($_GET['idsp'])) {
    $idsp = $_GET['idsp'];

    // Truy vấn dữ liệu sản phẩm từ cơ sở dữ liệu
    $stmt = $pdo->prepare("SELECT * FROM sanpham WHERE idsp = :idsp");
    $stmt->bindParam(':idsp', $idsp, PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode([
            'name' => $product['tensp'],
            'image' => $product['anh'] 
        ]);
    } else {
        echo json_encode(['error' => 'Sản phẩm không tồn tại.']);
    }
}
?>
