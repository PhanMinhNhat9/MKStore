<?php
require_once '../config.php';
$pdo = connectDatabase();
if (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id']));

    try {
        if ($_SESSION['user']['quyen'] != 2589 && $id != $_SESSION['user']['iduser']) {
            throw new Exception('Bạn không có quyền xóa người dùng này!');
        }

        // Fetch email from users table (assuming a users table exists)
        $stmtUser = $pdo->prepare("SELECT email FROM user WHERE iduser = :iduser");
        $stmtUser->execute(['iduser' => $id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['email'])) {
            throw new Exception('Không tìm thấy email của người dùng!');
        }
        $email = $user['email'];

        $stmt = $pdo->prepare("INSERT INTO khxoatk (iduser, trangthai) VALUES (:iduser, :trangthai)");
        $result = $stmt->execute([
            'iduser' => $id,
            'trangthai' => 1 
        ]);
        if ($result) {
            header("Location: guiemail.php?id=" . urlencode($id) . "&email=" . urlencode($email));
            exit();
        }
        
    } catch (Exception $e) {
        $errorMsg = addslashes($e->getMessage());
    }
} 
?>
