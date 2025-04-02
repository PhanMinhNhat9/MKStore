<?php
require_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập.']);
    exit();
}

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu user_id.']);
    exit();
}

$userId = (int)$_GET['user_id'];
$currentUserId = $_SESSION['user']['iduser'];
$pdo = connectDatabase();

try {
    $stmt = $pdo->prepare("
        SELECT m.*, u.hoten AS sender_name
        FROM messages m
        JOIN user u ON m.sender_id = u.iduser
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.timestamp ASC
    ");
    $stmt->execute([$currentUserId, $userId, $userId, $currentUserId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $messages]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
