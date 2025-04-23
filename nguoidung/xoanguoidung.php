<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Người Dùng</title>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../trangchuadmin.js"></script>
</head>
<body>
<?php
require_once '../config.php';
$pdo = connectDatabase();
if (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id']));

    try {
        // Check permissions
        if ($_SESSION['user']['quyen'] != 2589 && $id != $_SESSION['user']['iduser']) {
            throw new Exception('Bạn không có quyền xóa người dùng này!');
        }



        // Delete user
        // if (xoaNguoiDung($id)) {
            // Insert into khxoatk
            $stmt = $pdo->prepare("INSERT INTO khxoatk (iduser, trangthai) VALUES (:iduser, :trangthai)");
            $stmt->execute([
                'iduser' => $id,
                'trangthai' => 1 // Assuming 1 means deleted
            ]);

            // Check if the deleted user is the logged-in user
            if ($id == $_SESSION['user']['iduser']) {
                // Show session expiration and account deletion notice
                echo "
                <script>
                    showCustomAlert('🐳 Phiên Làm Việc Hết Hạn', 'Phiên làm việc của bạn đã hết hạn, và tài khoản của bạn sẽ được xóa hoàn toàn sau 30 ngày.', '../picture/success.png');
                    setTimeout(function() {
                        window.top.location.href = '../logout.php';
                    }, 3000);
                </script>";
            } 
        // } else {
        //     throw new Exception('Lỗi khi xóa người dùng!');
        // }
    } catch (Exception $e) {
        $errorMsg = addslashes($e->getMessage());
        echo "
        <script>
            showCustomAlert('🐳 Xóa Không Thành Công!', '$errorMsg', '../picture/error.png');
        </script>";
    }
} else {
    echo "
    <script>
        showCustomAlert('🐳 Lỗi!', 'Không tìm thấy ID người dùng!', '../picture/error.png');
        setTimeout(function() {
            window.location.href = 'user_management.php';
        }, 3000);
    </script>";
}
?>
</body>
</html>