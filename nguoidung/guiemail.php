<?php
include "../config.php";
$pdo = connectDatabase();

if (isset($_GET['email']) && isset($_GET['id'])) {
  
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    $iduser = intval($_GET['id']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT hoten FROM user WHERE iduser = :iduser");
        $stmt->execute(['iduser' => $iduser]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception('Không tìm thấy thông tin người dùng!');
        }

        $tenkh = $user['hoten'] ?: 'Khách hàng';
        guimailthongbaoxoaTK($email, $tenkh);
        echo "<script>window.top.location.href = '../trangchu.php?status=KHXoaT';</script>";
    } catch (Exception $e) {
        $errorMsg = addslashes($e->getMessage());
        echo "<script>window.top.location.href = '../trangchu.php?status=KHXoaF';</script>";
    }
} 
?>