<?php
include "config.php";
$pdo = connectDatabase();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $stmt = $pdo->prepare("SELECT trangthai FROM trangthaidb WHERE iduser = :iduser");
    $stmt->execute(['iduser' => $_SESSION['user']['iduser']]);
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($status['trangthai'] == 0) {
        echo json_encode(['status' => 'inactive']);

    } else {
        echo json_encode(['status' => 'active']);
    }
} catch (PDOException $e) {
    error_log("Error checking status: " . $e->getMessage());
    echo json_encode(['status' => 'error']);
}
?>