<?php
require_once '../config.php';
$pdo = connectDatabase();

header('Content-Type: application/json');

if (isset($_GET['phone'])) {
    $phone = $_GET['phone'];

    $stmt = $pdo->prepare("SELECT hoten FROM user WHERE sdt = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'hoten' => $user['hoten']]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
