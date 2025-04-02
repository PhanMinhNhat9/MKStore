<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập.']);
    exit();
}

if (!isset($_POST['message']) || !isset($_POST['receiver_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu.']);
    exit();
}

$message = trim($_POST['message']);
$receiverId = (int)$_POST['receiver_id'];
$senderId = $_SESSION['user']['iduser'];

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Tin nhắn không được để trống.']);
    exit();
}

$pdo = connectDatabase();

try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, $message]);

    echo json_encode(['status' => 'success', 'message' => 'Tin nhắn đã được gửi.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
