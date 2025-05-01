<?php
require_once '../config.php';

// Kết nối CSDL
$pdo = connectDatabase();

// Thiết lập header trả về JSON
header('Content-Type: application/json');

// Đọc dữ liệu JSON từ body
$input = json_decode(file_get_contents('php://input'), true);

// Lấy dữ liệu từ JSON
$idctdh = $input['idctdh'] ?? '';
$idsp = $input['idsp'] ?? '';
$rating = $input['rating'] ?? 0;
$content = $input['content'] ?? '';
$idkh = $input['idkh'] ?? '';
$thoigian = $input['thoigian'] ?? date('Y-m-d H:i:s'); // Sử dụng thời gian từ client hoặc server

try {
    // Kiểm tra dữ liệu đầu vào
    if (empty($idctdh) || empty($idsp) || empty($idkh) || $rating === 0 || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không đầy đủ!']);
        exit;
    }

    // Kiểm tra đánh giá trùng lặp
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM danhgia WHERE idctdh = :idctdh AND idkh = :idkh");
    $stmt->execute(['idctdh' => $idctdh, 'idkh' => $idkh]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá đơn hàng này rồi!']);
        exit;
    }

    // Thêm dữ liệu vào bảng danhgia
    $sql = "INSERT INTO danhgia (idctdh, idkh, idsp, sosao, noidung, thoigian) 
            VALUES (:idctdh, :idkh, :idsp, :sosao, :noidung, :thoigian)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'idctdh' => $idctdh,
        'idkh' => $idkh,
        'idsp' => $idsp,
        'sosao' => $rating,
        'noidung' => $content,
        'thoigian' => $thoigian
    ]);

    // Cập nhật dữ liệu vào bảng danhgia
    $sql = "UPDATE `chitietdonhang` SET `danhgia`=1 WHERE idctdh = :idctdh";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'idctdh' => $idctdh
    ]);

    echo json_encode(['success' => true, 'message' => 'Đánh giá đã được lưu!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>