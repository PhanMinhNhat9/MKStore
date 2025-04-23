<?php
header('Content-Type: application/json');
require_once '../config.php';
$pdo = connectDatabase();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $iduser = isset($data['iduser']) ? (int)$data['iduser'] : 0;

    if ($iduser > 0) {
        try {
            // Xóa bản ghi trong bảng khxoatk
            $stmt = $pdo->prepare("DELETE FROM khxoatk WHERE iduser = :iduser AND trangthai = 1");
            $stmt->bindValue(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Tài khoản không ở trạng thái lên lịch xóa.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'ID người dùng không hợp lệ.';
    }
} else {
    $response['message'] = 'Yêu cầu không hợp lệ.';
}

echo json_encode($response);
?>