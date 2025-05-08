<?php
require_once '../config.php';
$pdo = connectDatabase();

$id_nhan = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
$id_gui = $_SESSION['user']['iduser'];

if ($id_nhan <= 0) {
    die(json_encode(['error' => 'ID người nhận không hợp lệ']));
}

// Cập nhật trạng thái tin nhắn đã xem
$update_sql = "UPDATE chattructuyen SET daxem = 1 WHERE idgui = :id_nhan AND idnhan = :id_gui AND daxem = 0";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->execute([
    'id_nhan' => $id_nhan,
    'id_gui' => $id_gui
]);

// Truy vấn danh sách tin nhắn
$sql = "SELECT * FROM chattructuyen 
        WHERE (idgui = :id_gui AND idnhan = :id_nhan) 
           OR (idgui = :id_nhan_2 AND idnhan = :id_gui_2) 
        ORDER BY thoigian ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_gui' => $id_gui, 
    ':id_nhan' => $id_nhan,
    ':id_nhan_2' => $id_nhan, 
    ':id_gui_2' => $id_gui
]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['messages' => $messages]);
?>