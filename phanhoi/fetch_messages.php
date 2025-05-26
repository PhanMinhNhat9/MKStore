<?php
require_once '../config.php';
$pdo = connectDatabase();

if (!isset($_SESSION['user']['iduser'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id_nhan = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;
$id_gui = $_SESSION['user']['iduser'];

if ($id_nhan <= 0) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid recipient ID']);
    exit();
}

$sql = "SELECT idgui, noidung, thoigian, daxem FROM chattructuyen 
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

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['messages' => $messages]);
exit();
?>